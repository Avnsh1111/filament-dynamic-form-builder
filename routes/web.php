<?php

use Illuminate\Support\Facades\Route;
use Avnsh1111\FilamentDynamicFormBuilder\Http\Controllers\PublicFormController;

// Add more flexibility to route configuration with explicit domain option
Route::group([
    'prefix' => config('filament-dfb.route_prefix'),
    'middleware' => config('filament-dfb.middleware', ['web']),
    // Domain is optional and defaults to current domain
    'domain' => config('filament-dfb.domain', null),
], function () {
    Route::get('/{slug}', [PublicFormController::class, 'show'])->name('filament-dfb.show');
    Route::post('/{slug}', [PublicFormController::class, 'submit'])->name('filament-dfb.submit');
});
