<?php

use Illuminate\Support\Facades\Route;

// Servir SPA (Single Page Application)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*')->name('app');