<?php

namespace Avnsh1111\FilamentDynamicFormBuilder;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Avnsh1111\FilamentDynamicFormBuilder\Livewire\DynamicFormComponent;

class FilamentDynamicFormBuilderServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-dynamic-form-builder';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile('filament-dfb')
            ->hasViews('filament-dfb')
            ->hasMigrations();
            
        // Explicitly publish config with the tag mentioned in README
        $this->publishes([
            __DIR__ . '/../config/filament-dfb.php' => config_path('filament-dfb.php'),
        ], 'filament-dfb-config');
        
        // Explicitly publish migrations with the tag mentioned in README
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'filament-dfb-migrations');
    }

    public function packageBooted(): void
    {
        // Register Livewire components - use proper Livewire 3 syntax
        Livewire::component('av-dynamic-form', DynamicFormComponent::class);

        // Register plugin assets (scoped to this package)
        FilamentAsset::register([
            Css::make('filament-dfb', __DIR__ . '/../resources/css/filament-dfb.css'),
            Js::make('filament-dfb', __DIR__ . '/../resources/js/filament-dfb.js'),
        ], package: 'avnsh1111/filament-dynamic-form-builder');
        
        // Manually register routes to ensure they're loaded in Filament v4
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
