<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormEntryResource\Pages;

use Avnsh1111\FilamentDynamicFormBuilder\Resources\DynamicFormEntryResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            ->schema([
                Section::make('Form Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('id')
                            ->label('Entry ID'),

                        \Filament\Infolists\Components\TextEntry::make('form.name')
                            ->label('Form Name'),

                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                    ]),
                    
                $this->buildFormDataSection($rawData),
                
                $this->buildMetadataSection($rawData),
            ]);
    }
    
    protected function buildFormDataSection($rawData): Section
    {
        $formData = $this->parseJsonData($rawData->data ?? '{}');

        $entries = [];
        foreach ($formData as $key => $value) {
            $entries[] = \Filament\Infolists\Components\TextEntry::make("form_data.{$key}")
                ->label($key)
                ->state($value);
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
            ->schema($entries)
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
}
