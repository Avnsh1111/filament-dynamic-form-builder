<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicForm extends Model
{
    use HasFactory;

    protected $table = 'dynamic_forms';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'schema',         // JSON array from Builder
        'success_message',
        'is_active',
        'notify_emails',  // Comma-separated list of emails to notify
        'email_subject',  // Subject for notification emails
        'email_template', // Custom email template content
        'send_copy_to_submitter', // Whether to send a copy to the submitter
    ];

    protected $casts = [
        'schema' => 'array',
        'is_active' => 'boolean',
        'send_copy_to_submitter' => 'boolean',
        'notify_emails' => 'array',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(DynamicFormEntry::class);
    }
    
    /**
     * Get the list of form fields as field name => field label pairs
     * Useful for email templates to show available field tokens
     * 
     * @return array
     */
    public function getFormFieldsMap(): array
    {
        $fieldsMap = [];
        
        if (!$this->schema || !is_array($this->schema)) {
            return $fieldsMap;
        }
        
        foreach ($this->schema as $block) {
            $type = $block['type'] ?? $block['data']['type'] ?? null;
            $data = $block['data'] ?? [];
            
            // Skip if not a field component or missing required data
            if (!$type || $type === 'section' || $type === 'section_end' || empty($data['name'])) {
                continue;
            }
            
            $fieldsMap[$data['name']] = $data['label'] ?? $data['name'];
        }
        
        return $fieldsMap;
    }
    
    /**
     * Process an email template with form data
     * 
     * @param array $formData
     * @return string
     */
    public function renderEmailTemplate(array $formData): string
    {
        $template = $this->email_template ?? "Thank you for your submission.\n\n";
        
        // Replace field tokens with actual values
        foreach ($formData as $field => $value) {
            // Skip honeypot field
            if ($field === config('filament-dfb.honeypot')) {
                continue;
            }
            
            // Format array values
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            
            $template = str_replace('{'.$field.'}', $value, $template);
        }
        
        return $template;
    }
}
