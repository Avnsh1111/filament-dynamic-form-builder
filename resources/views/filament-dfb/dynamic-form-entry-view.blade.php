@php
    $record = $this->record;
@endphp

<div class="filament-page">
    <div class="filament-page-content">
        <div class="filament-section">
            <div class="filament-section-header">
                <h1 class="filament-section-heading text-2xl font-bold tracking-tight">
                    View Form Entry: {{ $record->form->name ?? 'N/A' }}
                </h1>
            </div>

            <div class="filament-section-content space-y-6">
                <!-- Top Row: Metadata on the right half -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-1">
                        <!-- Empty space on the left -->
                    </div>
                    @if($record->meta)
                        <div class="col-span-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Submission Metadata
                            </h3>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                                <pre class="text-sm">{{ json_encode($record->meta, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bottom Row: Form Data taking full width -->
                <div class="mt-8">
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Form Data</h3>
                        <pre class="text-sm">{{ json_encode($record->data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>

            <div class="filament-section-footer mt-6">
                <div class="flex justify-start">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        &larr; Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
