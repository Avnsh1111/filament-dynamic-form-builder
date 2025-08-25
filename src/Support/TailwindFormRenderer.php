<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Support;

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class TailwindFormRenderer
{
    /**
     * Convert form schema into Tailwind HTML with Livewire bindings
     *
     * @param array $schema
     * @param array|null $data Form data for pre-filling values
     * @return HtmlString
     */
    public static function render(array $schema, ?array $data = null): HtmlString
    {
        $html = '';
        
        foreach ($schema as $block) {
            $type = $block['type'] ?? $block['data']['type'] ?? null;
            $blockData = $block['data'] ?? [];
            
            if (!$type) continue;
            
            // Handle section blocks with nested fields
            if ($type === 'section') {
                $html .= self::renderSection($blockData, $data);
                continue;
            }
            
            // For non-section blocks, render standard field components
            $fieldHtml = self::renderField($type, $blockData, $data);
            if ($fieldHtml) {
                $html .= $fieldHtml;
            }
        }
        
        return new HtmlString($html);
    }
    
    /**
     * Render a form section with nested fields
     * 
     * @param array $sectionData
     * @param array|null $data Form data for pre-filling values
     * @return string
     */
    protected static function renderSection(array $sectionData, ?array $data = null): string
    {
        $heading = $sectionData['heading'] ?? 'Section';
        $description = $sectionData['description'] ?? '';
        $collapsible = !empty($sectionData['collapsible']);
        $collapsed = !empty($sectionData['collapsed']);
        
        // Create section ID for collapsible functionality
        $sectionId = 'section-' . Str::slug($heading) . '-' . uniqid();
        
        $html = '<div class="form-section mb-6">';
        
        // Section header
        if ($collapsible) {
            $html .= '<div class="collapsible-section-header" x-data="{ open: ' . ($collapsed ? 'false' : 'true') . ' }">';
            $html .= '<div class="flex justify-between items-center cursor-pointer" @click="open = !open">';
            $html .= '<h3 class="text-lg font-medium text-gray-900">' . e($heading) . '</h3>';
            $html .= '<span x-show="!open" class="text-gray-500">+</span>';
            $html .= '<span x-show="open" class="text-gray-500">−</span>';
            $html .= '</div>';
            
            if ($description) {
                $html .= '<p class="text-sm text-gray-600 mt-1">' . e($description) . '</p>';
            }
            
            $html .= '<div x-show="open" class="mt-4 space-y-4">';
        } else {
            $html .= '<div class="form-section-header">';
            $html .= '<h3 class="text-lg font-medium text-gray-900">' . e($heading) . '</h3>';
            
            if ($description) {
                $html .= '<p class="text-sm text-gray-600 mt-1">' . e($description) . '</p>';
            }
            
            $html .= '</div>';
            $html .= '<div class="mt-4 space-y-4">';
        }
        
        // Process nested fields
        if (!empty($sectionData['fields']) && is_array($sectionData['fields'])) {
            foreach ($sectionData['fields'] as $fieldBlock) {
                $fieldType = $fieldBlock['type'] ?? null;
                $fieldData = $fieldBlock['data'] ?? [];
                
                if (!$fieldType) continue;
                
                $fieldHtml = self::renderField($fieldType, $fieldData, $data);
                if ($fieldHtml) {
                    $html .= $fieldHtml;
                }
            }
        }
        
        // Close section
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a form field based on its type
     * 
     * @param string $type
     * @param array $data
     * @param array|null $formData
     * @return string
     */
    protected static function renderField(string $type, array $data, ?array $formData = null): string
    {
        return match ($type) {
            'text_input' => self::renderTextInput($data, $formData),
            'textarea' => self::renderTextarea($data, $formData),
            'select' => self::renderSelect($data, $formData),
            'checkbox' => self::renderCheckbox($data, $formData),
            'toggle' => self::renderToggle($data, $formData),
            'number' => self::renderNumber($data, $formData),
            'date_time' => self::renderDateTime($data, $formData),
            'file_upload' => self::renderFileUpload($data, $formData),
            'rich_editor' => self::renderRichEditor($data, $formData),
            'radio' => self::renderRadio($data, $formData),
            'checkbox_list' => self::renderCheckboxList($data, $formData),
            default => ''
        };
    }
    
    /**
     * Get field value from form data if available
     */
    protected static function getValue(string $name, ?array $data, $default = null)
    {
        return $data[$name] ?? $default;
    }
    
    /**
     * Render a standard field wrapper with label
     */
    protected static function fieldWrapper(string $name, string $label, string $input, bool $required = false, ?string $hint = null): string
    {
        $labelClasses = 'block text-sm font-medium text-gray-700';
        $requiredMark = $required ? '<span class="text-red-500 ml-1">*</span>' : '';
        
        $html = '<div class="form-field mb-4">';
        $html .= '<label for="' . e($name) . '" class="' . $labelClasses . '">' . e($label) . $requiredMark . '</label>';
        
        if ($hint) {
            $html .= '<p class="form-field-description text-xs text-gray-500 mt-1">' . e($hint) . '</p>';
        }
        
        $html .= $input;
        $html .= '</div>';
        
        return $html;
    }
    
    protected static function renderTextInput(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $placeholder = $data['placeholder'] ?? '';
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData, $data['default'] ?? '');
        
        $input = '<input 
            type="text" 
            id="' . e($name) . '" 
            name="' . e($name) . '" 
            value="' . e($value) . '" 
            x-model="formData.' . e($name) . '"
            ' . ($placeholder ? 'placeholder="' . e($placeholder) . '"' : '') . ' 
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md form-input" 
            ' . ($required ? 'required' : '') . '
        >';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderTextarea(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $placeholder = $data['placeholder'] ?? '';
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData, $data['default'] ?? '');
        $rows = $data['rows'] ?? 3;
        
        $input = '<textarea 
            id="' . e($name) . '" 
            name="' . e($name) . '" 
            rows="' . intval($rows) . '" 
            x-model="formData.' . e($name) . '"
            ' . ($placeholder ? 'placeholder="' . e($placeholder) . '"' : '') . ' 
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md form-input" 
            ' . ($required ? 'required' : '') . '
        >' . e($value) . '</textarea>';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderSelect(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData);
        $options = $data['options'] ?? [];
        $multiple = !empty($data['multiple']);
        
        $input = '<select 
            id="' . e($name) . '" 
            name="' . e($name) . ($multiple ? '[]' : '') . '" 
            x-model="formData.' . e($name) . '"
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm form-select" 
            ' . ($required ? 'required' : '') . '
            ' . ($multiple ? 'multiple' : '') . '
        >';
        
        // Add empty option for non-multiple selects
        if (!$multiple) {
            $input .= '<option value="">Select an option</option>';
        }
        
        // Add options
        foreach ($options as $option) {
            $optionValue = $option['value'] ?? $option['label'] ?? '';
            $optionLabel = $option['label'] ?? $option['value'] ?? '';
            $selected = '';
            
            if ($multiple && is_array($value) && in_array($optionValue, $value)) {
                $selected = 'selected';
            } elseif (!$multiple && $value == $optionValue) {
                $selected = 'selected';
            }
            
            $input .= '<option value="' . e($optionValue) . '" ' . $selected . '>' . e($optionLabel) . '</option>';
        }
        
        $input .= '</select>';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderRadio(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData);
        $options = $data['options'] ?? [];
        
        // Check if this is a rating field
        $isRating = str_contains(strtolower($name), 'rate') || 
            str_contains(strtolower($label), 'rate') ||
            self::isRatingOptionSet($options);
        
        $inputWrapper = '<div class="' . ($isRating ? 'rating-options flex flex-wrap gap-3 mt-2' : 'space-y-2 mt-2') . '">';
        
        // Add radio options
        foreach ($options as $i => $option) {
            $optionValue = $option['value'] ?? $option['label'] ?? '';
            $optionLabel = $option['label'] ?? $option['value'] ?? '';
            $optionId = $name . '_' . $i;
            $checked = $value == $optionValue ? 'checked' : '';
            
            if ($isRating) {
                // Rating option style
                $inputWrapper .= '
                <div class="rating-option">
                    <input type="radio" id="' . e($optionId) . '" name="' . e($name) . '" value="' . e($optionValue) . '" ' . $checked . ' class="absolute opacity-0 w-0 h-0">
                    <label for="' . e($optionId) . '" class="cursor-pointer px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                        ' . e($optionLabel) . '
                    </label>
                </div>';
            } else {
                // Standard radio style
                $inputWrapper .= '
                <div class="flex items-center">
                    <input type="radio" id="' . e($optionId) . '" name="' . e($name) . '" value="' . e($optionValue) . '" ' . $checked . ' class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                    <label for="' . e($optionId) . '" class="ml-2 block text-sm text-gray-700">
                        ' . e($optionLabel) . '
                    </label>
                </div>';
            }
        }
        
        $inputWrapper .= '</div>';
        
        return self::fieldWrapper($name, $label, $inputWrapper, $required);
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
        foreach ($options as $option) {
            $optionValue = $option['value'] ?? '';
            $optionLabel = $option['label'] ?? '';
            
            $lowerLabel = strtolower($optionLabel);
            if (in_array($lowerLabel, $ratingTerms) || 
                in_array($optionLabel, $numericalScale) ||
                in_array($optionValue, $numericalScale)) {
                $matchCount++;
            }
        }
        
        // If most options match rating terms, consider it a rating scale
        return $matchCount >= min(count($options) * 0.7, 3);
    }
    
    protected static function renderCheckbox(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData);
        $checked = $value ? 'checked' : '';
        
        $input = '
        <div class="flex items-center mt-1">
            <input 
                type="checkbox" 
                id="' . e($name) . '" 
                name="' . e($name) . '" 
                value="1" 
                ' . $checked . ' 
                x-model="formData.' . e($name) . '"
                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" 
                ' . ($required ? 'required' : '') . '
            >
            <label for="' . e($name) . '" class="ml-2 block text-sm text-gray-700">
                ' . e($label) . ($required ? '<span class="text-red-500 ml-1">*</span>' : '') . '
            </label>
        </div>';
        
        // Override standard wrapper for checkboxes
        return '<div class="form-field mb-4">' . $input . '</div>';
    }
    
    protected static function renderToggle(array $data, ?array $formData): string
    {
        // Similar to checkbox but with a toggle style
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData);
        
        $input = '
        <div class="flex items-center mt-1" x-data="{ checked: ' . ($value ? 'true' : 'false') . ' }">
            <button type="button" 
                @click="checked = !checked; formData.' . e($name) . ' = checked ? 1 : 0" 
                x-bind:class="{\'bg-indigo-600\': checked, \'bg-gray-200\': !checked}"
                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                role="switch"
            >
                <span x-bind:class="{\'translate-x-5\': checked, \'translate-x-0\': !checked}"
                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                ></span>
            </button>
            <input type="hidden" id="' . e($name) . '" name="' . e($name) . '" x-model="formData.' . e($name) . '" x-bind:value="checked ? 1 : 0">
            <label for="' . e($name) . '" class="ml-2 block text-sm text-gray-700">
                ' . e($label) . ($required ? '<span class="text-red-500 ml-1">*</span>' : '') . '
            </label>
        </div>';
        
        // Override standard wrapper for toggles
        return '<div class="form-field mb-4">' . $input . '</div>';
    }
    
    protected static function renderNumber(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $placeholder = $data['placeholder'] ?? '';
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData, $data['default'] ?? '');
        $min = $data['min'] ?? '';
        $max = $data['max'] ?? '';
        $step = $data['step'] ?? '1';
        
        $input = '<input 
            type="number" 
            id="' . e($name) . '" 
            name="' . e($name) . '" 
            value="' . e($value) . '" 
            x-model="formData.' . e($name) . '"
            ' . ($placeholder ? 'placeholder="' . e($placeholder) . '"' : '') . ' 
            ' . ($min !== '' ? 'min="' . e($min) . '"' : '') . ' 
            ' . ($max !== '' ? 'max="' . e($max) . '"' : '') . ' 
            step="' . e($step) . '" 
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md form-input" 
            ' . ($required ? 'required' : '') . '
        >';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderDateTime(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData, $data['default'] ?? '');
        
        $input = '<input 
            type="datetime-local" 
            id="' . e($name) . '" 
            name="' . e($name) . '" 
            value="' . e($value) . '" 
            x-model="formData.' . e($name) . '"
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md form-input" 
            ' . ($required ? 'required' : '') . '
        >';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderFileUpload(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $multiple = !empty($data['multiple']);
        
        $input = '
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                    <label for="' . e($name) . '" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                        <span>Upload a file</span>
                        <input id="' . e($name) . '" name="' . e($name) . '" type="file" class="sr-only" ' . ($multiple ? 'multiple' : '') . ' ' . ($required ? 'required' : '') . '>
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
            </div>
        </div>';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderRichEditor(array $data, ?array $formData): string
    {
        // For a simple implementation, we'll just use a textarea with some basic styling
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $value = self::getValue($name, $formData, $data['default'] ?? '');
        
        $input = '<textarea 
            id="' . e($name) . '" 
            name="' . e($name) . '" 
            rows="6"
            x-model="formData.' . e($name) . '"
            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md form-input" 
            ' . ($required ? 'required' : '') . '
        >' . e($value) . '</textarea>';
        
        return self::fieldWrapper($name, $label, $input, $required);
    }
    
    protected static function renderCheckboxList(array $data, ?array $formData): string
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? $name;
        $required = !empty($data['required']);
        $values = self::getValue($name, $formData, []);
        $options = $data['options'] ?? [];
        
        if (!is_array($values)) {
            $values = [];
        }
        
        $inputWrapper = '<div class="space-y-2 mt-1">';
        
        // Add checkbox options
        foreach ($options as $i => $option) {
            $optionValue = $option['value'] ?? $option['label'] ?? '';
            $optionLabel = $option['label'] ?? $option['value'] ?? '';
            $optionId = $name . '_' . $i;
            $checked = in_array($optionValue, $values) ? 'checked' : '';
            
            $inputWrapper .= '
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="' . e($optionId) . '" 
                    name="' . e($name) . '[]" 
                    value="' . e($optionValue) . '" 
                    ' . $checked . '
                    @change="if(!formData.' . e($name) . ') { formData.' . e($name) . ' = []; } 
                             if($event.target.checked) {
                                 formData.' . e($name) . '.push(\'' . e($optionValue) . '\');
                             } else {
                                 formData.' . e($name) . ' = formData.' . e($name) . '.filter(v => v !== \'' . e($optionValue) . '\');
                             }"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                >
                <label for="' . e($optionId) . '" class="ml-2 block text-sm text-gray-700">
                    ' . e($optionLabel) . '
                </label>
            </div>';
        }
        
        $inputWrapper .= '</div>';
        
        return self::fieldWrapper($name, $label, $inputWrapper, $required);
    }
}
