<?php

return [
    'general' => [
        'headline' => 'Favicon Generator',
        'generate' => 'Save and generate',
        'description' => 'Generate favicons for your website',
    ],
    'settings' => [
        'title' => 'Favicon Generator Settings',
        'description' => 'Configure favicons for Statamic',
        'save' => 'Save settings',
        'saved' => 'Settings saved',
        'unable_to_save' => 'Unable to save settings. Please fix the errors and try again.',
        'api_key' => 'Realfavicon API Key',
        'api_key_description' => 'You can get your API key from <a href="https://realfavicongenerator.net/api/#register_key" target="_blank">realfavicongenerator.net</a>.',
        'icon' => 'Favicon master',
        'icon_description' => 'This image will be used to generate all the favicons. It should be at least 260x260px.',
    ],

    'unable_to_save' => 'Unable to save settings. Please fix the errors and try again.',

    'modal' => [
        'loading_title' => 'Generating Favicons…',
        'loading_hint' => 'This may take a few seconds.',
        'error_title' => 'Generation failed',
        'close' => 'Close',
    ],

    'errors' => [
        'missing_inputs' => 'Missing API key or master image.',
        'local_url' => 'The selected image is hosted on a local URL and cannot be fetched by RealFaviconGenerator. Please use a publicly reachable image URL (this works automatically on your production deployment).',
        'generation_failed' => 'Favicon generation failed.',
        'unexpected' => 'Unexpected error during favicon generation.',
    ],

    'success' => [
        'generated' => 'Saved and generated',
    ],

];