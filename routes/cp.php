<?php

use Illuminate\Support\Facades\Route;

Route::namespace('\Laborb\FaviconGenerator\Http\Controllers\Cp')
    ->prefix('favicon-generator/')
    ->name('laborb.favicon-generator.')
    ->group(function () {
        Route::get('/', 'FaviconController@index')->name('index');
        Route::post('/update', 'FaviconController@update')->name('update');
    });