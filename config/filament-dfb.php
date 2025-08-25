<?php

return [
    // Public route prefix for out-of-panel form rendering.
    'route_prefix' => 'forms',

    // Optional domain for public routes (null to use current domain)
    'domain' => null,

    // Middleware for public routes.
    'middleware' => ['web'],

    // Honeypot field to deter bots. If present, submission will be ignored.
    'honeypot' => '_hp',

    // Default success message on submission.
    'success_message' => 'Thanks! Your response has been recorded.',

    // Whether to store requester IP & user agent.
    'store_meta' => true,
    
    // Email notification settings
    'email' => [
        // Default subject for notification emails
        'default_subject' => 'New form submission: {form_name}',
        
        // Email layout view
        'layout_view' => 'filament-dfb::emails.layout',
        
        // From address for notification emails
        'from_address' => null, // Uses default mail.from in Laravel if null
        
        // From name for notification emails
        'from_name' => null, // Uses default mail.from in Laravel if null
    ],
    
    // Navigation and menu settings
    'navigation' => [
        // Navigation group name for Filament admin panel
        'group' => 'Forms',
        
        // Navigation labels for resources
        'labels' => [
            'forms' => 'Dynamic Forms',
            'entries' => 'Form Entries',
        ],
        
        // Navigation icons for resources
        'icons' => [
            'forms' => 'heroicon-o-rectangle-stack',
            'entries' => 'heroicon-o-document-text',
        ],
        
        // Sort order for the navigation items
        'sort' => [
            'forms' => 1,
            'entries' => 2,
        ],
        
        // Whether to show navigation badge with count of entries
        'show_badges' => true,
    ],
];
