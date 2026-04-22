<?php

namespace Laborb\FaviconGenerator\Http\Controllers\Cp;

use Illuminate\Http\Request;
use Laborb\FaviconGenerator\Blueprints\Favicons;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\File;
use Statamic\Facades\YAML;
use Statamic\Facades\Asset;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class FaviconController extends CpController
{
    protected $path;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $blueprint = Favicons::blueprint();
        $data = Favicons::values();

        $fields = $blueprint->fields()->addValues($data)->preProcess();

        return view('favicons::cp.settings.index', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    public function update(Request $request)
    {
        $blueprint = Favicons::blueprint();

        $fields = $blueprint->fields()->addValues($request->all());

        $fields->validate();

        File::put(config('statamic.favicons.path'), YAML::dump($fields->process()->values()->all()));

        // Generate favicons
        return $this->generate($request);
    }

	public function generate(Request $request) {
		try {
			$apiKey = Favicons::values()['api_key'] ?? null;
			$masterImage = Favicons::augmentedValues()['icon']->value()['permalink'] ?? null;

			if (!$apiKey || !$masterImage) {
				return response()->json([
					'status' => 'error',
					'msg' => 'Missing API key or master image.'
				], 200);
			}

			$masterImageHost = parse_url($masterImage, PHP_URL_HOST);
			if (is_string($masterImageHost)) {
				$masterImageHost = strtolower($masterImageHost);
				if (
					str_ends_with($masterImageHost, '.ddev.site')
					|| $masterImageHost === 'localhost'
					|| $masterImageHost === '127.0.0.1'
				) {
					return response()->json([
						'status' => 'error',
						'msg' => 'The selected image is hosted on a local URL and cannot be fetched by RealFaviconGenerator. Use a publicly reachable image URL.'
					], 200);
				}
			}

			$apiUrl = 'https://realfavicongenerator.net/api/favicon';
			$filesLocationPath = '/' . Favicons::getAssetsContainer()['id'] . '/';
			$normalizedMasterImage = $this->normalizeMasterImage($masterImage, $request, $filesLocationPath);
			$masterImageForApi = $normalizedMasterImage ?: $masterImage;

			// Overwrite config values
			$payload = config('statamic.favicons.payload');
			$payload['favicon_generation']['api_key'] = $apiKey;
			$payload['favicon_generation']['master_picture']['url'] = $masterImageForApi;
			$payload['favicon_generation']['files_location']['path'] = $filesLocationPath;
			$payload['favicon_generation']['versioning']['param_value'] = Str::random(6);

			$response = Http::timeout(120)->post($apiUrl, $payload);

			if ($response->successful() && $response->json('favicon_generation_result.result.status') == 'success') {

				// Handle generated zip file
				$zipUrl = $response->json('favicon_generation_result.favicon.package_url');
				$zipFile = sys_get_temp_dir() . '/favicons.zip';

				file_put_contents($zipFile, file_get_contents($zipUrl));

				$zip = new ZipArchive;
				$zip->open($zipFile);

				$faviconsDirectory = public_path($filesLocationPath);

				$zip->extractTo($faviconsDirectory);
				$zip->close();

				unlink($zipFile);

				// Write new blueprint values
				$values = $request->all();

				$values['html_tags'] = $response->json('favicon_generation_result.favicon.html_code');
				$values['generated_at'] = now()->format('Y-m-d H:i:s');

				$blueprint = Favicons::blueprint();

				$fields = $blueprint->fields()->addValues($values);

				$fields->validate();

				File::put(config('statamic.favicons.path'), YAML::dump($fields->process()->values()->all()));

				return response()->json([
					'status' => 'success',
					'msg' => 'Saved and generated'
				], 200);
			}

			$errorMessage = $response->json('favicon_generation_result.result.error_message')
				?? $response->json('favicon_generation_result.result.status')
				?? $response->json('error')
				?? trim($response->body())
				?? 'Favicon generation failed.';

			if (is_string($errorMessage) && str_contains(strtolower($errorMessage), 'fetch failed')) {
				$errorMessage = 'The source image URL is not publicly reachable for RealFaviconGenerator. Please use a publicly accessible image URL (no local ddev/private host).';
			}

			if (is_array($errorMessage)) {
				$errorMessage = json_encode($errorMessage);
			}

			if (!$errorMessage) {
				$errorMessage = 'Favicon generation failed.';
			}

			Log::error('Favicon generation failed.', [
				'http_status' => $response->status(),
				'master_image' => $masterImage,
				'master_image_used' => $masterImageForApi,
				'response_body' => $response->body(),
			]);

			return response()->json([
				'status' => 'error',
				'msg' => $errorMessage,
			], 200);
		} catch (\Throwable $e) {
			Log::error('Favicon generation exception.', [
				'exception' => $e,
			]);

			return response()->json([
				'status' => 'error',
				'msg' => $e->getMessage() ?: 'Unexpected error during favicon generation.',
			], 200);
		}
	}

	private function normalizeMasterImage(string $masterImage, Request $request, string $filesLocationPath): ?string
	{
		try {
			$imageResponse = Http::timeout(30)->get($masterImage);
			if (!$imageResponse->successful()) {
				return null;
			}

			$binary = $imageResponse->body();
			if (!$binary) {
				return null;
			}

			$image = @imagecreatefromstring($binary);
			if ($image === false) {
				return null;
			}

			$targetDir = public_path($filesLocationPath);
			if (!is_dir($targetDir)) {
				mkdir($targetDir, 0755, true);
			}

			$targetFilename = '__rfg-master-' . Str::random(8) . '.png';
			$targetPath = rtrim($targetDir, '/') . '/' . $targetFilename;

			$written = @imagepng($image, $targetPath, 9);
			imagedestroy($image);

			if (!$written) {
				return null;
			}

			return rtrim($request->getSchemeAndHttpHost(), '/') . rtrim($filesLocationPath, '/') . '/' . $targetFilename;
		} catch (\Throwable $e) {
			Log::warning('Master image normalization failed.', [
				'master_image' => $masterImage,
				'exception' => $e,
			]);

			return null;
		}
	}
}