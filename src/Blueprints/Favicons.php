<?php

namespace Laborb\FaviconGenerator\Blueprints;

use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;
use Statamic\Facades\AssetContainer;

class Favicons
{
    public static function augmentedValues()
    {
        return static::blueprint()
            ->fields()
            ->addValues(static::values())
            ->augment()
            ->values();
    }

    public static function values()
    {
        return collect(YAML::file(config('statamic.favicons.path'))->parse())
            ->merge(YAML::file(config('statamic.favicons.path'))->parse())
            ->all();
    }

    public static function getAssetsContainer()
	{
		$configHandle = config('statamic.favicons.assetPath') ?? '';

		return AssetContainer::findByHandle($configHandle) ?? AssetContainer::all()->first();
	}

    public static function blueprint()
    {
        return Blueprint::make()->setContents([
            'sections' => [
                'main' => [
                    'display' => __('favicon-generator::cp.settings.title'),
                    'fields' => [
                        [
                            'handle' => 'settings_introduction',
                            'field' => [
                                'default' => true,
                                'display' => __('favicon-generator::cp.settings.title'),
                                'instructions' => __('favicon-generator::cp.settings.description'),
                                'type' => 'section',
                                'icon' => 'section',
                                'visibility' => 'visible',
                            ],
                        ],
                        [
                            'handle' => 'api_key',
                            'field' => [
                                'display' => __('favicon-generator::cp.settings.api_key'),
                                'instructions' => __('favicon-generator::cp.settings.api_key_description'),
                                'type' => 'text',
                                'icon' => 'text',
                                'visibility' => 'visible',
                                'required' => true,
                            ],
                        ],
                        [
                            'handle' => 'icon',
                            'field' => [
                                'mode' => 'list',
                                'max_files' => 1,
                                'show_filename' => true,
                                'allow_uploads' => true,
                                'container' => self::getAssetsContainer()->handle(),
                                'display' => __('favicon-generator::cp.settings.icon'),
                                'instructions' => __('favicon-generator::cp.settings.icon_description'),
                                'type' => 'assets',
                                'icon' => 'assets',
                                'visibility' => 'visible',
                                'required' => true,
                            ],
                        ],
                        [
                            'handle' => 'html_tags',
                            'field' => [
                                'type' => 'textarea',
                                'icon' => 'textarea',
                                'visibility' => 'hidden',
                                'listable' => false,
                            ],
                        ],
                        [
                            'handle' => 'generated_at',
                            'field' => [
                                'default' => 'never',
                                'type' => 'text',
                                'icon' => 'text',
                                'visibility' => 'hidden',
                                'listable' => false,
                            ],
                        ],
                    ]
                ]
            ],
        ]);
    }
}