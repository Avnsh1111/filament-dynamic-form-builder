@php
    $record = $this->record;
@endphp

<x-filament-panels::page>
    <x-filament-panels::section>
        <x-slot name="heading">
            View Form Entry: {{ $record->form->name ?? 'N/A' }}
        </x-slot>

        <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Form Data</h3>
            <pre class="text-sm">{{ json_encode($record->data, JSON_PRETTY_PRINT) }}</pre>
        </div>

        @if($record->meta)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Submission Metadata
                </h3>
                <div class="mt-4 rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                    <pre class="text-sm">{{ json_encode($record->meta, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <div class="flex justify-start">
                <x-filament::button color="gray" tag="a" :href="url()->previous()">
                    &larr; Back
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament-panels::section>
</x-filament-panels::page>
