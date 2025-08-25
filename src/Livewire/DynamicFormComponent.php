<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Livewire;

use Avnsh1111\FilamentDynamicFormBuilder\Models\DynamicForm;
use Avnsh1111\FilamentDynamicFormBuilder\Models\DynamicFormEntry;
use Avnsh1111\FilamentDynamicFormBuilder\Support\SchemaRenderer;
use Avnsh1111\FilamentDynamicFormBuilder\Support\TailwindFormRenderer;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Mail\Message;
use Livewire\Component;

class DynamicFormComponent extends Component
{
    public ?DynamicForm $form = null;
    public array $formData = [];
    public string $slug;
    public ?array $formSchema = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->form = DynamicForm::query()
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();
            
        $this->formSchema = $this->form->schema ?? [];
    }
    
    /**
     * Render the form using the TailwindFormRenderer
     * 
     * @return \Illuminate\Support\HtmlString
     */
    public function renderForm()
    {
        return TailwindFormRenderer::render($this->formSchema, $this->formData);
    }
    
    /**
     * Handle form submission
     */
    public function submit(): void
    {
        // Honeypot guard
        $hp = config('filament-dfb.honeypot');
        if (!empty($this->formData[$hp] ?? null)) {
            // silently drop
            return;
        }

        $entry = DynamicFormEntry::create([
            'dynamic_form_id' => $this->form->id,
            'data' => $this->formData,
            'meta' => config('filament-dfb.store_meta') ? [
                'ip' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ] : null,
            'user_id' => Auth::id(),
        ]);

        // Send email notifications if configured
        $this->sendEmailNotifications($this->formData, $entry);

        Notification::make()
            ->title($this->form->success_message ?: config('filament-dfb.success_message'))
            ->success()
            ->send();

        // Reset state
        $this->formData = [];

        // Updated to use Livewire v3 dispatch syntax
        $this->dispatch('dfb-submitted', ['id' => $entry->id]);
    }
    
    /**
     * Send email notifications for this form submission
     * 
     * @param array $formData The submitted form data
     * @param DynamicFormEntry $entry The saved form entry
     * @return void
     */
    protected function sendEmailNotifications(array $formData, DynamicFormEntry $entry): void
    {
        // Check if we have notification emails configured
        $notifyEmails = $this->form->notify_emails ?? [];
        if (empty($notifyEmails) && !$this->form->send_copy_to_submitter) {
            return;
        }
        
        // Get submitter email if needed
        $submitterEmail = null;
        if ($this->form->send_copy_to_submitter) {
            // Look for an email field in the form data
            foreach ($formData as $field => $value) {
                // Check for fields that might contain an email
                if (
                    (str_contains(strtolower($field), 'email') && filter_var($value, FILTER_VALIDATE_EMAIL)) || 
                    (isset($formData['email']) && filter_var($formData['email'], FILTER_VALIDATE_EMAIL))
                ) {
                    $submitterEmail = $value;
                    break;
                }
            }
        }
        
        // Generate subject and body from template
        $subject = $this->form->email_subject ?? 'Form Submission: ' . $this->form->name;
        
        // Replace field tokens in subject line
        foreach ($formData as $field => $value) {
            // Skip honeypot field
            if ($field === config('filament-dfb.honeypot')) {
                continue;
            }
            
            // Format array values
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            
            $subject = str_replace('{'.$field.'}', $value, $subject);
        }
        
        // Get the email body content
        $emailContent = $this->form->renderEmailTemplate($formData);
        $emailViewPath = config('filament-dfb.email.layout_view', 'filament-dfb::emails.layout');
        
        // Format email content for HTML display
        $formattedContent = nl2br($emailContent);
        
        // Send to notification emails
        if (!empty($notifyEmails)) {
            Mail::send([], [], function (Message $message) use ($notifyEmails, $subject, $formattedContent, $emailViewPath) {
                $message->to($notifyEmails)
                    ->subject($subject)
                    ->html(view($emailViewPath, ['content' => $formattedContent])->render());
            });
        }
        
        // Send copy to submitter if applicable
        if ($submitterEmail) {
            Mail::send([], [], function (Message $message) use ($submitterEmail, $subject, $formattedContent, $emailViewPath) {
                $message->to($submitterEmail)
                    ->subject($subject)
                    ->html(view($emailViewPath, ['content' => $formattedContent])->render());
            });
        }
    }

    public function render(): View
    {
        return view('filament-dfb::livewire.dynamic-form');
    }
}
