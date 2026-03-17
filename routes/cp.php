<?php

use Illuminate\Support\Facades\Route;
use Laborb\FaviconGenerator\Http\Controllers\Cp\FaviconController;

Route::prefix('favicon-generator')
    ->name('laborb.favicon-generator.')
    ->group(function () {
        Route::get('/', [FaviconController::class, 'index'])->name('index');
        Route::post('/update', [FaviconController::class, 'update'])->name('update');
    });