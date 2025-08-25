<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? ($form->name ?? config('app.name', 'Dynamic Form')) }}</title>

    <!-- Scripts -->
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Base styles */
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            line-height: 1.5;
        }
        
        /* Container styles */
        .container {
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (min-width: 640px) {
            .container {
                max-width: 640px;
            }
        }
        
        @media (min-width: 768px) {
            .container {
                max-width: 768px;
            }
        }
        
        @media (min-width: 1024px) {
            .container {
                max-width: 1024px;
            }
        }
        
        /* Breadcrumbs */
        .breadcrumbs {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .breadcrumbs a {
            color: #4f46e5;
            text-decoration: none;
        }
        
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        
        .breadcrumbs-separator {
            margin: 0 0.5rem;
            color: #9ca3af;
        }
        
        /* Form section styling */
        .form-section {
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            overflow: hidden;
        }
        
        .form-section-header {
            background-color: #f9fafb;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .form-section-content {
            padding: 1rem;
        }
        
        /* Form field styling */
        .form-field {
            margin-bottom: 1rem;
        }
        
        .form-field label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .form-field-description {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        /* Input styling */
        .form-input, 
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #fff;
            font-size: 0.875rem;
        }
        
        .form-input:focus, 
        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            border-color: #4f46e5;
            box-shadow: 0 0 0 1px #4f46e5;
        }
        
        /* Select styling */
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            appearance: none;
        }
        
        /* Radio and checkbox styling */
        .form-radio-group {
            margin-top: 0.5rem;
        }
        
        /* Rating options styling (special radio button group) */
        .rating-options {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        
        .rating-option {
            position: relative;
        }
        
        .rating-option input[type="radio"] {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
        }
        
        .rating-option label {
            cursor: pointer;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            color: #374151;
            transition: all 0.2s;
            display: block;
        }
        
        .rating-option input[type="radio"]:checked + label {
            background-color: #4f46e5;
            color: #fff;
            border-color: #4f46e5;
        }
        
        .rating-option input[type="radio"]:focus + label {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px #e0e7ff;
        }
        
        /* Submit button styling */
        button[type="submit"] {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        button[type="submit"]:hover {
            background-color: #4338ca;
        }
        
        button[type="submit"]:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px #e0e7ff;
        }
        
        button[type="submit"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Validation styling */
        input:invalid,
        textarea:invalid,
        select:invalid {
            border-color: #ef4444;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="container mx-auto py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ config('app.name', 'Dynamic Form Builder') }}
                    </h1>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="container mx-auto py-8">
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="p-6">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200">
            <div class="container mx-auto py-4">
                <div class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Dynamic Form Builder') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
