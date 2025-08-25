<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Support;

// Form components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Get;

// Using proper Schemas components for v4
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class SchemaRenderer
{
    /**
     * Convert saved Builder schema into Filament form components.
     *
     * @param array $schema
     * @return array
     */
    public static function toComponents(array $schema): array
    {
        $components = [];
        
        foreach ($schema as $block) {
            $type = $block['type'] ?? $block['data']['type'] ?? null; // v4 stores 'type' at top-level
            $data = $block['data'] ?? [];

            if (! $type) continue;

            // Handle section blocks with nested fields
            if ($type === 'section') {
                // Process the section with its fields
                $components[] = self::createSection($data);
                continue;
            }

            // For non-section blocks, create standard field components
            $component = self::createComponent($type, $data);
            if ($component === null) continue;
            
            $components[] = $component;
        }

        return $components;
    }
    
    /**
     * Create a section with its nested fields
     * 
     * @param array $sectionData
     * @return Section
     */
    protected static function createSection(array $sectionData): Section
    {
        $section = Section::make($sectionData['heading'] ?? 'Section');
        
        if (!empty($sectionData['description'])) {
            $section->description($sectionData['description']);
        }
        
        if (!empty($sectionData['collapsible'])) {
            $section->collapsible();
        }
        
        if (!empty($sectionData['collapsed'])) {
            $section->collapsed();
        }
        
        // Apply custom styling for sections to match screenshot
        $section->extraAttributes([
            'class' => 'form-section',
        ]);
        
        $section->headerActions([])
            ->extraHeaderAttributes(['class' => 'form-section-header']);
        
        // Set up the grid columns based on the section's grid setting
        $gridColumns = $sectionData['grid'] ?? 1;
        
        // Process nested fields if they exist
        $fields = [];
        if (!empty($sectionData['fields']) && is_array($sectionData['fields'])) {
            foreach ($sectionData['fields'] as $fieldBlock) {
                $fieldType = $fieldBlock['type'] ?? null;
                $fieldData = $fieldBlock['data'] ?? [];
                
                if (!$fieldType) continue;
                
                $fieldComponent = self::createComponent($fieldType, $fieldData);
                if ($fieldComponent) {
                    $fields[] = $fieldComponent;
                }
            }
        }
        
        return $section->schema($fields)
            ->columns($gridColumns)
            ->extraAttributes(['class' => 'form-section'])
            ->extraContentAttributes(['class' => 'form-section-content']);
    }
    
    /**
     * Create a component based on type and data
     * 
     * @param string $type
     * @param array $data
     * @return mixed
     */
    protected static function createComponent(string $type, array $data)
    {
        $component = match ($type) {
            'text_input' => self::textInput($data),
            'textarea' => self::textarea($data),
            'select' => self::select($data),
            'checkbox' => self::checkbox($data),
            'toggle' => self::toggle($data),
            'number' => self::number($data),
            'date_time' => self::dateTime($data),
            'file_upload' => self::fileUpload($data),
            'rich_editor' => self::richEditor($data),
            'radio' => self::radio($data),
            'checkbox_list' => self::checkboxList($data),
            default => null
        };
        
        if ($component === null) return null;

        // Apply custom attributes if any
        if (!empty($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attr) {
                if (!empty($attr['key']) && isset($attr['value'])) {
                    $component->extraAttributes([$attr['key'] => $attr['value']]);
                }
            }
        }

        // Apply column span if set (for layout)
        if (isset($data['columnSpan'])) {
            $component->columnSpan($data['columnSpan']);
        }
        
        // Apply generic field wrapper classes
        $component->extraAttributes([
            'class' => 'form-field',
        ]);
        
        // Make labels bold
        $component->labelAttributes([
            'class' => 'font-medium',
        ]);
        
        // Add required indicator
        if (!empty($data['required'])) {
            $component->markAsRequired();
        }
        
        return $component;
    }

    /**
     * Create a text input component.
     */
    protected static function textInput(array $data): TextInput
    {
        $component = TextInput::make($data['name'])
            ->label($data['label']);

        if (!empty($data['placeholder'])) {
            $component->placeholder($data['placeholder']);
        }

        if (!empty($data['required'])) {
            $component->required();
        }

        if (isset($data['default'])) {
            $component->default($data['default']);
        }

        if (!empty($data['regex'])) {
            $component->regex($data['regex']);
        }

        if (!empty($data['min'])) {
            $component->minLength((int) $data['min']);
        }

        if (!empty($data['max'])) {
            $component->maxLength((int) $data['max']);
        }
        
        // Apply custom styling for text inputs
        $component->extraAttributes([
            'class' => 'form-input',
        ]);

        return $component;
    }

    protected static function baseLabelRequired($component, array $data)
    {
        if (!empty($data['label'])) {
            $component->label($data['label']);
            $component->labelAttributes([
                'class' => 'font-medium',
            ]);
        }
        
        if (!empty($data['required'])) {
            $component->required();
            $component->markAsRequired();
        }
        
        if (!empty($data['hint'])) {
            $component->hint($data['hint']);
            $component->hintAttributes([
                'class' => 'form-field-description',
            ]);
        }
        
        return $component;
    }

    protected static function textarea(array $data)
    {
        $c = Textarea::make($data['name'] ?? 'textarea_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        
        if (!empty($data['placeholder'])) {
            $c->placeholder($data['placeholder']);
        }
        
        if (!empty($data['rows'])) {
            $c->rows((int) $data['rows']);
        }
        
        // Apply custom styling for textareas
        $c->extraAttributes([
            'class' => 'form-input',
        ]);
        
        return $c;
    }

    protected static function select(array $data)
    {
        $c = Select::make($data['name'] ?? 'select_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        $options = [];
        if (!empty($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $opt) {
                $options[$opt['value'] ?? $opt['label'] ?? ''] = $opt['label'] ?? $opt['value'] ?? '';
            }
        }
        $c->options($options);
        
        if (!empty($data['multiple'])) {
            $c->multiple();
        }
        
        if (!empty($data['searchable'])) {
            $c->searchable();
        }
        
        // Apply custom styling for select inputs
        $c->extraAttributes([
            'class' => 'form-select',
        ]);
        
        return $c;
    }

    protected static function checkbox(array $data)
    {
        $c = Checkbox::make($data['name'] ?? 'checkbox_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        
        if (!empty($data['default'])) {
            $c->default((bool) $data['default']);
        }
        
        return $c;
    }

    protected static function toggle(array $data)
    {
        $c = Toggle::make($data['name'] ?? 'toggle_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        
        if (!empty($data['default'])) {
            $c->default((bool) $data['default']);
        }
        
        return $c;
    }

    protected static function number(array $data)
    {
        $c = TextInput::make($data['name'] ?? 'number_' . uniqid())->numeric();
        $c = self::baseLabelRequired($c, $data);
        
        if (!empty($data['min'])) {
            $c->minValue((float) $data['min']);
        }
        
        if (!empty($data['max'])) {
            $c->maxValue((float) $data['max']);
        }
        
        if (!empty($data['step'])) {
            $c->step((float) $data['step']);
        }
        
        // Apply custom styling for number inputs
        $c->extraAttributes([
            'class' => 'form-input',
        ]);
        
        return $c;
    }

    protected static function dateTime(array $data)
    {
        $c = DateTimePicker::make($data['name'] ?? 'date_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        
        // Apply custom styling for date time pickers
        $c->extraAttributes([
            'class' => 'form-input',
        ]);
        
        return $c;
    }

    protected static function fileUpload(array $data)
    {
        $c = FileUpload::make($data['name'] ?? 'file_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        
        if (!empty($data['multiple'])) {
            $c->multiple();
        }
        
        if (!empty($data['directory'])) {
            $c->directory($data['directory']);
        }
        
        return $c;
    }

    protected static function richEditor(array $data)
    {
        $c = RichEditor::make($data['name'] ?? 'rich_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        return $c;
    }

    protected static function radio(array $data)
    {
        $c = Radio::make($data['name'] ?? 'radio_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        $options = [];
        
        if (!empty($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $opt) {
                $options[$opt['value'] ?? $opt['label'] ?? ''] = $opt['label'] ?? $opt['value'] ?? '';
            }
        }
        
        $c->options($options);
        
        // Apply custom styling for radio groups
        // Check if this is a rating field based on field name or options
        $isRating = str_contains(strtolower($data['name'] ?? ''), 'rate') || 
            str_contains(strtolower($data['label'] ?? ''), 'rate') ||
            self::isRatingOptionSet($options);
            
        if ($isRating) {
            $c->extraAttributes([
                'class' => 'rating-options',
            ])->inline();
            
            // Add special attributes for the options
            $c->optionsAttributes(fn () => [
                'class' => 'rating-option',
            ]);
            
            // Force button-like styling
            $c->buttonLabel();
        } else {
            $c->extraAttributes([
                'class' => 'form-radio-group',
            ]);
        }
        
        return $c;
    }
    
    /**
     * Check if the options represent a rating scale (One, Two, Three, etc. or 1-5)
     */
    protected static function isRatingOptionSet(array $options): bool
    {
        $ratingTerms = ['one', 'two', 'three', 'four', 'five'];
        $numericalScale = ['1', '2', '3', '4', '5'];
        
        // Count how many options match rating terms
        $matchCount = 0;
        foreach ($options as $value => $label) {
            $lowerLabel = strtolower($label);
            if (in_array($lowerLabel, $ratingTerms) || 
                in_array($label, $numericalScale) ||
                in_array($value, $numericalScale)) {
                $matchCount++;
            }
        }
        
        // If most options match rating terms, consider it a rating scale
        return $matchCount >= min(count($options) * 0.7, 3);
    }

    protected static function checkboxList(array $data)
    {
        $c = CheckboxList::make($data['name'] ?? 'checkbox_list_' . uniqid());
        $c = self::baseLabelRequired($c, $data);
        $options = [];
        
        if (!empty($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $opt) {
                $options[$opt['value'] ?? $opt['label'] ?? ''] = $opt['label'] ?? $opt['value'] ?? '';
            }
        }
        
        $c->options($options);
        
        return $c;
    }
}
