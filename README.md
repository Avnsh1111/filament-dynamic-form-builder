# Filament Dynamic Form Builder (Filament v4)

A Filament v4 plugin that lets you **design dynamic forms**, **render them anywhere**, **store submissions**, and **send email notifications**.

## Features
- Visual form designer using Filament's `Builder` field (text, textarea, select, radio, checkbox, toggle, number, date-time, file upload, rich editor).
- **Form sections and layout control** - create organized forms with sections and flexible field layouts.
- **Custom HTML attributes** - add custom attributes to any form field for advanced styling and behavior.
- **Email notifications** - send form submissions to multiple recipients with customizable templates.
- Runtime renderer (`<livewire:av-dynamic-form slug="..." />`) that converts saved schema to Filament form components on the fly.
- Submission storage, basic spam honeypot, and per-form success message.
- Filament Resources to manage Forms and Entries from your panel.
- Publishable views, config, routes, and migrations.
- Asset registration via Filament Asset Manager (v4).

## Install
```bash
composer require avnsh1111/filament-dynamic-form-builder
php artisan vendor:publish --tag=filament-dfb-config
php artisan vendor:publish --tag=filament-dfb-migrations
php artisan migrate
php artisan filament:assets
```

Register in your panel provider:
```php
use Avnsh1111\FilamentDynamicFormBuilder\DynamicFormBuilderPlugin;

public function panel(\Filament\Panel $panel): \Filament\Panel
{
    return $panel
        ->plugin(DynamicFormBuilderPlugin::make()
            ->navigationGroup('Content')
            ->navigationIcon('heroicon-o-rectangle-stack'));
}
```

Render a form (frontend or anywhere Blade can run Livewire):
```blade
<livewire:av-dynamic-form slug="contact-us" />
```

## Config
See `config/filament-dfb.php` for route prefix, middleware, spam honeypot field name, email settings, etc.

## Advanced Features

### Form Sections and Layouts
You can now organize your forms with sections and control the layout of fields:

- **Sections** - Create sections to organize your form fields into logical groups
- **Collapsible Sections** - Make sections collapsible for better user experience with longer forms
- **Layout Controls** - Specify fields per row (1, 2, 3, or 4) for each section
- **Field Column Spans** - Control field widths with column span settings

### Custom HTML Attributes
Add custom HTML attributes to any form field:

- **Data Attributes** - Add data attributes for JavaScript integrations
- **CSS Classes** - Add custom CSS classes for styling
- **ARIA Attributes** - Add accessibility attributes
- **Any HTML Attribute** - Any key-value pair can be added as an attribute

### Email Notifications
Configure email notifications for form submissions:

- **Multiple Recipients** - Send notifications to multiple email addresses
- **Custom Templates** - Create custom email templates with form field tokens
- **Dynamic Subject Lines** - Use form field values in email subjects
- **Copy to Submitter** - Optionally send a copy of the submission to the form submitter

#### Using Field Tokens in Templates
In both subject lines and email templates, you can use field tokens in the format `{field_name}`:

```
Thank you for contacting us, {name}!

We've received your message and will respond to {email} shortly.

Your message:
{message}
```

## Contributing
Contributions are welcome. Please feel free to submit a Pull Request.

## License
MIT
