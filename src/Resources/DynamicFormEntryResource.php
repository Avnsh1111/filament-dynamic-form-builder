<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Resources;

use Avnsh1111\FilamentDynamicFormBuilder\Models\DynamicFormEntry;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DynamicFormEntryResource extends Resource
{
    protected static ?string $model = DynamicFormEntry::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    public static function getNavigationIcon(): string
    {
        return config('filament-dfb.navigation.icons.entries', 'heroicon-o-document-text');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return config('filament-dfb.navigation.group', 'Forms');
    }
    
    public static function getNavigationLabel(): string
    {
        return config('filament-dfb.navigation.labels.entries', 'Form Entries');
    }
    
    public static function getNavigationSort(): ?int
    {
        return config('filament-dfb.navigation.sort.entries', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return config('filament-dfb.navigation.show_badges', true) 
            ? static::getModel()::count() 
            : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('form.name')->label('Form')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Actions\ViewAction::make()->label('')->tooltip('View')->button()->size('sm'),
                Actions\DeleteAction::make()->label('')->tooltip('Delete')->button()->size('sm'),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DynamicFormEntryResource\Pages\ListDynamicFormEntries::route('/'),
            'view' => DynamicFormEntryResource\Pages\ViewDynamicFormEntry::route('/{record}'),
        ];
    }
}
