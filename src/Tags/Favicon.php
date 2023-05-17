<?php

namespace Laborb\FaviconGenerator\Tags;

use Statamic\Tags\Tags;
use Laborb\FaviconGenerator\Blueprints\Favicons;
use Illuminate\Support\Facades\Log;

class Favicon extends Tags
{
    /**
     * The {{ favicon }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        return '<!-- laborb Favicon Generator Tags -->'
            . Favicons::augmentedValues()['html_tags']
            . '<!-- /laborb Favicon Generator Tags -->';
    }
}