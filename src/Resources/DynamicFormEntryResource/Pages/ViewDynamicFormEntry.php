<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormEntryResource\Pages;

use Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormEntryResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action as InfolistAction;

class ViewDynamicFormEntry extends ViewRecord
{
    protected static string $resource = DynamicFormEntryResource::class;

    public function infolist(Schema $schema): Schema
    {
        $record = $this->getRecord();
        $recordId = $record->id;
        
        // Get raw data directly from the database
        $rawData = DB::table('dynamic_form_entries')->where('id', $recordId)->first();
        
        return $schema
            ->columns(2)
            ->schema([
                // Form Details section - left column
                Section::make('Form Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('id')
                            ->label('Entry ID'),

                        \Filament\Infolists\Components\TextEntry::make('form.name')
                            ->label('Form Name'),

                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                    ])
                    ->columnSpan(1),
                    
                // Metadata section - right column
                $this->buildMetadataSection($rawData)
                    ->columnSpan(1),
                
                // Form Data section - full width bottom
                $this->buildFormDataSection($rawData)
                    ->columnSpanFull(),
            ]);
    }
    
    protected function buildFormDataSection($rawData): Section
    {
        $formData = $this->parseJsonData($rawData->data ?? '{}');
        $record = $this->getRecord();
        $formSchema = $record->form->schema ?? [];

        $entries = [];
        foreach ($formData as $key => $value) {
            // Check if this field is a file upload field
            $fieldType = $this->getFieldType($key, $formSchema);
            
            if ($fieldType === 'file_upload' && !empty($value)) {
                // Handle file upload fields with view/download actions
                $entries[] = $this->createFileUploadEntry($key, $value);
            } else {
                // Handle regular fields
                $entries[] = \Filament\Infolists\Components\TextEntry::make("form_data.{$key}")
                    ->label($key)
                    ->state($this->formatFieldValue($value));
            }
        }
        
        if (empty($entries)) {
            $entries[] = \Filament\Infolists\Components\TextEntry::make('no_form_data')
                ->label('Notice')
                ->state('No form data available');
        }
        
        return Section::make('Form Data')
            ->schema($entries)
            ->collapsible();
    }
    
    protected function buildMetadataSection($rawData): Section
    {
        $metaData = $this->parseJsonData($rawData->meta ?? '{}');
        
        $entries = [];
        foreach ($metaData as $key => $value) {
            $entries[] = \Filament\Infolists\Components\TextEntry::make("meta_data.{$key}")
                ->label($key)
                ->state($value);
        }
        
        if (empty($entries)) {
            $entries[] = \Filament\Infolists\Components\TextEntry::make('no_meta_data')
                ->label('Notice')
                ->state('No metadata available');
        }
        
        return Section::make('Metadata')
            ->schema([
                Grid::make(1)
                    ->schema($entries)
            ])
            ->collapsible();
    }
    
    protected function parseJsonData($data)
    {
        // Handle various data formats that might be stored
        if (is_array($data)) {
            return $data; // Already an array
        }
        
        if (empty($data)) {
            return [];
        }
        
        // If it's a string, try to decode it as JSON
        if (is_string($data)) {
            // Handle cases where the string might have escaped quotes
            $cleaned = str_replace('\"', '"', $data);
            $cleaned = trim($cleaned, '"'); // Remove surrounding quotes if present
            
            // Try JSON decode first
            try {
                $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    return $decoded;
                }
            } catch (\Exception $e) {
                // Failed JSON decode with original string
            }
            
            // Try with cleaned string
            try {
                $decoded = json_decode($cleaned, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    return $decoded;
                }
            } catch (\Exception $e) {
                // Failed JSON decode with cleaned string
            }
            
            // Handle serialized data (used by some Laravel packages)
            if (strpos($data, 'O:') === 0 || strpos($data, 'a:') === 0) {
                try {
                    $unserialized = unserialize($data);
                    if (is_array($unserialized)) {
                        return $unserialized;
                    }
                } catch (\Exception $e) {
                    // Failed unserialize
                }
            }
        }
        
        // If all parsing attempts fail, return empty array
        return [];
    }
    
    /**
     * Get the field type from the form schema
     */
    protected function getFieldType(string $fieldName, array $formSchema): ?string
    {
        foreach ($formSchema as $block) {
            $data = $block['data'] ?? [];
            if (isset($data['name']) && $data['name'] === $fieldName) {
                return $block['type'] ?? null;
            }
        }
        return null;
    }
    
    /**
     * Create a file upload entry with view/download actions
     */
    protected function createFileUploadEntry(string $key, $value): Group
    {
        $files = is_array($value) ? $value : [$value];
        
        $actions = [];
        foreach ($files as $index => $filePath) {
            if (empty($filePath)) continue;
            
            $fileName = basename($filePath);
            $actions[] = InfolistAction::make("view_file_{$key}_{$index}")
                ->label($fileName)
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->url(fn () => Storage::url($filePath))
                ->openUrlInNewTab();
                
//            $actions[] = InfolistAction::make("download_file_{$key}_{$index}")
//                ->label('Download')
//                ->icon('heroicon-o-arrow-down-tray')
//                ->color('success')
//                ->action(fn () => Storage::download($filePath, $fileName));
        }
        
        return Group::make([
            \Filament\Infolists\Components\TextEntry::make("file_label_{$key}")
                ->label($key)
                ->state(count($files) . ' file(s) uploaded')
                ->columnSpanFull(),
            Actions::make($actions)
                ->columnSpanFull()
        ]);
    }
    
    /**
     * Format field value for display
     */
    protected function formatFieldValue($value): string
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }
        
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        
        return (string) $value;
    }
}
