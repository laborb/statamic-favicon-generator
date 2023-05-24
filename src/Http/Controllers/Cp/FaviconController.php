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
		$apiKey = Favicons::values()['api_key'];
		$masterImage = Favicons::augmentedValues()['icon']->value()['permalink'];

		$apiUrl = 'https://realfavicongenerator.net/api/favicon';
		$filesLocationPath = '/' . Favicons::getAssetsContainer()['id'] . '/';

		// Overwrite config values
		$payload = config('statamic.favicons.payload');
		$payload['favicon_generation']['api_key'] = $apiKey;
		$payload['favicon_generation']['master_picture']['url'] = $masterImage;
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
		} else {
			Log::error($response->json());
			
			return response()->json([
				'status' => 'error',
				'msg' => $response->json('favicon_generation_result.result.error_message')
			], 200);
		}
	}
}