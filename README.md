# Favicon Generator

Favicon Generator is a Statamic addon that automatically generates all necessary favicons via the realfavicon.net API. Once configured you and your customers can regenerate all favicons with just a single click in the Statamic control panel.

## Features

This addon covers all features of the realfavicon.net favicon generator. 

- Web Favicons
- Mobile Device App Icons
- Search and Pin Tab Icons
- Safari and IOS Backgrounds and Margins
- Android App Icons

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require laborb/statamic-favicon-generator
```

Then you need to add the `{{ favicon }}` Antlers Tag between the `<head>` and `</head>` tag in your layout.

## How to Use

After you installed this addon you'll find options in the `Utilities > Favicons` section of the Statamic control panel.
There you can set up your free realfavicon.net API key and define a master Favicon.

Click `Save and generate` to save the settings and generate all configured favicons.

## How to configure

### Publish config file

``` bash
php artisan vendor:publish --tag=favicon-generator-config
```

### Assets container

You can configure a Statamic assets container to store all generated favicons in `config/statamic/favicons.php`. If set to `null` the first asset container found will be used. Statamics default is `Assets`.

### Optional icon generation

By default the addon will only generate standard web favicons.
You can configure other icons by uncommenting and editing the option settings in the `config/statamic/favicons.php` config file after you published it.

You can find all available options here: https://realfavicongenerator.net/api/non_interactive_api

## License

The MIT License (MIT). Please see LICENSE.md for more information.