<?php

namespace Avnsh1111\FilamentDynamicFormBuilder;

use Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormEntryResource;
use Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class DynamicFormBuilderPlugin implements Plugin
{
    protected ?string $navigationGroup = null;
    protected ?string $navigationIcon = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-dynamic-form-builder';
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;
        return $this;
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;
        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            DynamicFormResource::class,
            DynamicFormEntryResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // no-op
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon;
    }
}
