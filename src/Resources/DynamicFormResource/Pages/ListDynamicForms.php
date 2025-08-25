<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormResource\Pages;

use Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDynamicForms extends ListRecords
{
    protected static string $resource = DynamicFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
