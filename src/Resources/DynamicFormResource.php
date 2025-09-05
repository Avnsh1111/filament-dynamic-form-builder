<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Resources;

use Avnsh1111\FilamentDynamicFormBuilder\DynamicFormBuilderPlugin;
use Avnsh1111\FilamentDynamicFormBuilder\Models\DynamicForm;
use Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormResource\Pages;
use BackedEnum;
use Enum;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class DynamicFormResource extends Resource
{
    protected static ?string $model = DynamicForm::class;

    protected static string|BackedEnum|null $navigationIcon = null;
    
    protected static string|UnitEnum|null $navigationGroup = null;
    
    protected static ?string $navigationLabel = null;
    
    protected static ?int $navigationSort = null;

    public static function getNavigationIcon(): string
    {
        return config('filament-dfb.navigation.icons.forms', 'heroicon-o-rectangle-stack');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return config('filament-dfb.navigation.group', 'Forms');
    }
    
    public static function getNavigationLabel(): string
    {
        return config('filament-dfb.navigation.labels.forms', 'Dynamic Forms');
    }
    
    public static function getNavigationSort(): ?int
    {
        return config('filament-dfb.navigation.sort.forms', 1);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Form Details')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('name')->required()->live(onBlur: true),
                                    TextInput::make('slug')
                                        ->unique(ignoreRecord: true)
                                        ->required()
                                        ->helperText('Used in public route & Livewire component mounting.')
                                        ->dehydrateStateUsing(fn ($state, $get) => $state ?: \Str::slug($get('name'))),
                                    Toggle::make('is_active')->default(true)->inline(false),
                                    TextInput::make('success_message')->label('Success message')->default('Thanks! Your response has been recorded.'),
                                ]),
                            Textarea::make('description')->columnSpanFull(),
                        ]),
                        
                    Tabs\Tab::make('Form Builder')
                        ->schema([
                            Builder::make('schema')
                                ->label('Form Schema')
                                ->collapsed()
                                ->collapsible()
                                ->blockNumbers(false)
                                ->blocks([

                                    // Section block
//                                    Builder\Block::make('section')
//                                        ->label('Section')
//                                        ->schema([
//
//                                                    Grid::make(2)
//                                                        ->schema([
//                                                            TextInput::make('heading')->required(),
//                                                            Textarea::make('description'),
//                                                            Toggle::make('collapsible'),
//                                                            Toggle::make('collapsed')
//                                                                ->visible(fn (Get $get): bool => $get('collapsible')),
//                                                            Forms\Components\Select::make('grid')
//                                                                ->label('Layout Grid')
//                                                                ->options([
//                                                                    1 => '1 field per row',
//                                                                    2 => '2 fields per row',
//                                                                    3 => '3 fields per row',
//                                                                    4 => '4 fields per row',
//                                                                ])
//                                                                ->default(2)
//                                                                ->selectablePlaceholder(false),
//                                                        ]),
//
//
//                                            Builder::make('fields')
//                                                ->label('Section Fields')
//                                                ->collapsible()
//                                                ->collapsed()
//                                                ->blocks([
//                                                    // Text input block
//                                                    Builder\Block::make('text_input')
//                                                        ->label('Text Input')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->live(onBlur: true)
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            TextInput::make('placeholder'),
//                                                                            Toggle::make('required'),
//                                                                            TextInput::make('default'),
//                                                                            TextInput::make('min')->numeric()->minValue(0)->nullable(),
//                                                                            TextInput::make('max')->numeric()->nullable(),
//                                                                            TextInput::make('regex')->helperText('Optional validation regex.'),
//                                                                        ]),
//
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Textarea block
//                                                    Builder\Block::make('textarea')
//                                                        ->label('Textarea')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            TextInput::make('placeholder'),
//                                                                            Toggle::make('required'),
//                                                                            TextInput::make('rows')->numeric()->default(3),
//                                                                        ]),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Rich Editor block
//                                                    Builder\Block::make('rich_editor')
//                                                        ->label('Rich Editor')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                        ]),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default('full')
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Number block
//                                                    Builder\Block::make('number')
//                                                        ->label('Number')
//                                                        ->schema([
//
//                                                                    Grid::make(1)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                            TextInput::make('default')->numeric(),
//                                                                            TextInput::make('min')->numeric()->nullable(),
//                                                                            TextInput::make('max')->numeric()->nullable(),
//                                                                            TextInput::make('step')->numeric()->nullable(),
//                                                                        ]),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Select block
//                                                    Builder\Block::make('select')
//                                                        ->label('Select')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            TextInput::make('placeholder'),
//                                                                            Toggle::make('required'),
//                                                                            Toggle::make('multiple'),
//                                                                            Toggle::make('searchable'),
//                                                                        ]),
//
//                                                                    Forms\Components\Repeater::make('options')
//                                                                        ->schema([
//                                                                            TextInput::make('label')->required(),
//                                                                            TextInput::make('value')->required(),
//                                                                        ])
//                                                                        ->defaultItems(2)
//                                                                        ->collapsed()
//                                                                        ->columnSpanFull(),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Checkbox block
//                                                    Builder\Block::make('checkbox')
//                                                        ->label('Checkbox')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                            Toggle::make('default'),
//                                                                        ]),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Toggle block
//                                                    Builder\Block::make('toggle')
//                                                        ->label('Toggle')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                            Toggle::make('default'),
//                                                                        ]),
//
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Date Time block
//                                                    Builder\Block::make('date_time')
//                                                        ->label('Date & Time')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                            Forms\Components\Select::make('type')
//                                                                                ->label('Date/Time Type')
//                                                                                ->options([
//                                                                                    'datetime' => 'Date and Time',
//                                                                                    'date' => 'Date only',
//                                                                                    'time' => 'Time only',
//                                                                                ])
//                                                                                ->default('datetime')
//                                                                                ->required(),
//                                                                        ]),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // File Upload block
//                                                    Builder\Block::make('file_upload')
//                                                        ->label('File Upload')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                            Toggle::make('multiple'),
//                                                                            TextInput::make('directory')->helperText('Storage directory (optional)'),
//                                                                        ]),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Radio block
//                                                    Builder\Block::make('radio')
//                                                        ->label('Radio')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                        ]),
//
//                                                                    Forms\Components\Repeater::make('options')
//                                                                        ->schema([
//                                                                            TextInput::make('label')->required(),
//                                                                            TextInput::make('value')->required(),
//                                                                        ])
//                                                                        ->defaultItems(2)
//                                                                        ->collapsed()
//                                                                        ->columnSpanFull(),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//
//                                                    // Checkbox List block
//                                                    Builder\Block::make('checkbox_list')
//                                                        ->label('Checkbox List')
//                                                        ->schema([
//
//                                                                    Grid::make(2)
//                                                                        ->schema([
//                                                                            TextInput::make('name')
//                                                                                ->required()
//                                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
//                                                                                ->helperText('Key (a-z, 0-9, underscore).')
//                                                                                ->live(onBlur: true),
//
//                                                                            TextInput::make('label')
//                                                                                ->required()
//                                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
//
//                                                                            Toggle::make('required'),
//                                                                        ]),
//
//                                                                    Forms\Components\Repeater::make('options')
//                                                                        ->schema([
//                                                                            TextInput::make('label')->required(),
//                                                                            TextInput::make('value')->required(),
//                                                                        ])
//                                                                        ->defaultItems(3)
//                                                                        ->collapsed()
//                                                                        ->columnSpanFull(),
//
//                                                            Forms\Components\Select::make('columnSpan')
//                                                                ->label('Column Width')
//                                                                ->options([
//                                                                    1 => 'Default (1 column)',
//                                                                    2 => 'Wide (2 columns)',
//                                                                    'full' => 'Full width',
//                                                                ])
//                                                                ->default(1)
//                                                                ->columnSpanFull(),
//
//                                                            Forms\Components\Repeater::make('attributes')
//                                                                ->label('Custom HTML Attributes')
//                                                                ->schema([
//                                                                    TextInput::make('key'),
//                                                                    TextInput::make('value'),
//                                                                ])
//                                                                ->collapsed()
//                                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
//                                                                ->columnSpanFull(),
//                                                        ]),
//                                                ])->columnSpanFull(),
//                                        ]),



                                    // Text input block
                                    Builder\Block::make('text_input')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Text Input')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->live(onBlur: true)
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            TextInput::make('placeholder'),
                                                            Toggle::make('required'),
                                                            TextInput::make('default'),
                                                            TextInput::make('min')->numeric()->minValue(0)->nullable(),
                                                            TextInput::make('max')->numeric()->nullable(),
                                                            TextInput::make('regex')->helperText('Optional validation regex.'),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ])->columns(1),

                                    Builder\Block::make('textarea')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Textarea')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')->required()->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/'),
                                                            TextInput::make('label')->required()->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),
                                                            TextInput::make('placeholder'),
                                                            Toggle::make('required'),
                                                            TextInput::make('rows')->numeric()->default(3),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ])->columns(1),

                                    Builder\Block::make('select')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Select')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            TextInput::make('placeholder'),
                                                            Toggle::make('required'),
                                                            Toggle::make('multiple'),
                                                            Toggle::make('searchable'),
                                                        ]),

                                                    Forms\Components\Repeater::make('options')
                                                        ->schema([
                                                            TextInput::make('label')->required(),
                                                            TextInput::make('value')->required(),
                                                        ])
                                                        ->defaultItems(2)
                                                        ->collapsed()
                                                        ->columnSpanFull(),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ])->columns(1),
                                    Builder\Block::make('checkbox')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Checkbox')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                            Toggle::make('default'),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ])->columns(1),

                                    Builder\Block::make('toggle')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Toggle')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                            Toggle::make('default'),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ]),

                                    Builder\Block::make('number')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Number')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                            TextInput::make('default')->numeric(),
                                                            TextInput::make('min')->numeric()->nullable(),
                                                            TextInput::make('max')->numeric()->nullable(),
                                                            TextInput::make('step')->numeric()->nullable(),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ]),

                                    Builder\Block::make('date_time')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Date & Time')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                            Forms\Components\Select::make('type')
                                                                ->label('Date/Time Type')
                                                                ->options([
                                                                    'datetime' => 'Date and Time',
                                                                    'date' => 'Date only',
                                                                    'time' => 'Time only',
                                                                ])
                                                                ->default('datetime')
                                                                ->required(),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ]),

                                    Builder\Block::make('file_upload')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'File Upload')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                            Toggle::make('multiple'),
                                                            TextInput::make('directory')->helperText('Storage directory (optional)'),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ]),

                                    Builder\Block::make('rich_editor')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Rich Editor')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                        ]),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default('full')
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ])->columns(1),

                                    Builder\Block::make('radio')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Radio')
                                        ->schema([

                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('name')
                                                        ->required()
                                                        ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                        ->helperText('Key (a-z, 0-9, underscore).')
                                                        ->live(onBlur: true),

                                                    TextInput::make('label')
                                                        ->required()
                                                        ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                    Toggle::make('required'),
                                                ]),

                                            Forms\Components\Repeater::make('options')
                                                ->schema([
                                                    TextInput::make('label')->required(),
                                                    TextInput::make('value')->required(),
                                                ])
                                                ->defaultItems(2)
                                                ->collapsed()
                                                ->columnSpanFull(),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ]),

                                    Builder\Block::make('checkbox_list')
                                        ->label(fn (array $state = null): string => $state['label'] ?? 'Checkbox List')
                                        ->schema([

                                                    Grid::make(2)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->required()
                                                                ->regex('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                                                                ->helperText('Key (a-z, 0-9, underscore).')
                                                                ->live(onBlur: true),

                                                            TextInput::make('label')
                                                                ->required()
                                                                ->dehydrateStateUsing(fn ($state, $get) => $state ?: ucwords(str_replace('_', ' ', $get('name')))),

                                                            Toggle::make('required'),
                                                        ]),

                                                    Forms\Components\Repeater::make('options')
                                                        ->schema([
                                                            TextInput::make('label')->required(),
                                                            TextInput::make('value')->required(),
                                                        ])
                                                        ->defaultItems(3)
                                                        ->collapsed()
                                                        ->columnSpanFull(),

                                            Forms\Components\Select::make('columnSpan')
                                                ->label('Column Width')
                                                ->options([
                                                    1 => 'Default (1 column)',
                                                    2 => 'Wide (2 columns)',
                                                    'full' => 'Full width',
                                                ])
                                                ->default(1)
                                                ->columnSpanFull(),

                                            Forms\Components\Repeater::make('attributes')
                                                ->label('Custom HTML Attributes')
                                                ->schema([
                                                    TextInput::make('key'),
                                                    TextInput::make('value'),
                                                ])
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                ->columnSpanFull(),
                                        ])->columns(1),
                                ])->columnSpanFull(),
                        ]),
                        
                    Tabs\Tab::make('Email Notifications')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('email_subject')
                                        ->label('Email Subject')
                                        ->placeholder('Form Submission: {name}')
                                        ->helperText('You can use form field tokens like {field_name}'),
                                    Toggle::make('send_copy_to_submitter')
                                        ->label('Send copy to submitter')
                                        ->helperText('If checked, and the form has an "email" field, a copy of the submission will be sent to the submitter')
                                        ->inline(false),
                                ]),
                            Forms\Components\TagsInput::make('notify_emails')
                                ->label('Notification Emails')
                                ->placeholder('Add an email address')
                                ->helperText('Emails that will receive form submissions')
                                ->columnSpanFull(),
                            Section::make('Email Template')
                                ->schema([
                                    Forms\Components\MarkdownEditor::make('email_template')
                                        ->label('Email Template')
                                        ->placeholder('Thank you for your submission.')
                                        ->helperText('You can use field tokens like {field_name} which will be replaced with the submission values')
                                        ->columnSpanFull(),
                                        
                                    Forms\Components\Placeholder::make('available_tokens')
                                        ->label('Available Field Tokens')
                                        ->content(function ($record) {
                                            if (!$record) return 'Save form first to see available tokens';
                                            
                                            $fields = $record->getFormFieldsMap();
                                            if (empty($fields)) return 'No fields defined in form schema';
                                            
                                            $tokens = [];
                                            foreach ($fields as $name => $label) {
                                                $tokens[] = "{{$name}} - $label";
                                            }
                                            
                                            return implode('<br>', $tokens);
                                        })
                                        ->columnSpanFull(),
                                ])
                                ->collapsible()
                                ->collapsed(true),
                        ]),
                ])
                ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->copyable(),
                TextColumn::make('entries_count')->counts('entries')->label('Entries'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->actions([
                Actions\EditAction::make()->label('')->tooltip('Edit')->button()->size('sm'),
                Actions\Action::make('preview')
                    ->icon('heroicon-m-eye')
                    ->label('')->tooltip('Preview')->button()->size('sm')
                    ->url(fn (DynamicForm $record) => url(config('filament-dfb.route_prefix', 'forms') . '/' . $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDynamicForms::route('/'),
            'create' => Pages\CreateDynamicForm::route('/create'),
            'edit' => Pages\EditDynamicForm::route('/{record}/edit'),
        ];
    }
}
