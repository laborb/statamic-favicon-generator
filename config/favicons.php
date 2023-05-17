<?php

/*
 *  Configure your realfavicon API Options in the "payload" attribute.
 * 
 *  You can customize the options in >favicon_design< as you wish.
 *  See all available options here: https://realfavicongenerator.net/api/non_interactive_api
 * 
 */

return [
    'path' => base_path('content/addons/favicons.yaml'),
    'assetPath' => null,
    'payload' => [
        'favicon_generation' => [
            'master_picture' => [
                'type' => 'url',
            ],
            'files_location' => [
                'type' => 'path',
            ],
            'favicon_design' => [
                'desktop_browser' => [],

                // 'ios' => [],

                // 'windows' => [],

                // 'firefox_app' => [
                //     'manifest' => [
                //         'app_name' => config('app.name')
                //     ]
                // ],

                // 'android_chrome' => [
                //     'manifest' => [
                //         'name' => config('app.name')
                //     ]
                // ],

                // 'safari_pinned_tab' => [],

                // 'coast' => [],

                // 'open_graph' => [],

                // 'yandex_browser' => [
                //     'manifest' => [
                //         'show_title' => true,
                //         'version' => '1.0'
                //     ]
                // ]
            ],
            'settings' => [
                'compression' => '3',
                'scaling_algorithm' => 'Mitchell',
                'error_on_image_too_small' => true,
                'readme_file' => false,
                'html_code_file' => false,
                'use_path_as_is' => false,
            ],
            'versioning' => [
                'param_name' => 'v',
            ],
        ],
    ]
];