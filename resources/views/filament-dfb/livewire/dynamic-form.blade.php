<div class="form-container" x-data="{ 
    formData: @entangle('formData').live,
    isSubmitting: false,
    errors: {},
    
    validateForm() {
        this.errors = {};
        let isValid = true;
        
        // Find all required fields
        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                this.errors[field.name] = 'This field is required';
                isValid = false;
            } else if (field.type === 'email' && !this.validateEmail(field.value)) {
                this.errors[field.name] = 'Please enter a valid email address';
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    
    submitForm() {
        if (this.validateForm()) {
            this.isSubmitting = true;
            $wire.submit().then(() => {
                this.isSubmitting = false;
                window.scrollTo(0, 0);
            });
        }
    }
}">
    <form wire:submit.prevent="submitForm">
        <div class="form-content">
            {!! $this->renderForm() !!}
        </div>

        @if(config('filament-dfb.honeypot'))
            <div class="hidden">
                <input type="text" name="{{ config('filament-dfb.honeypot') }}" wire:model.live="formData.{{ config('filament-dfb.honeypot') }}">
            </div>
        @endif
        
        <!-- Show validation errors -->
        <template x-if="Object.keys(errors).length > 0">
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Please fix the following errors:
                        </p>
                        <template x-for="(errorMessage, field) in errors" :key="field">
                            <p class="text-sm text-red-700" x-text="errorMessage"></p>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        
        <div class="mt-6">
            <button type="submit" @click.prevent="submitForm" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150" :disabled="isSubmitting">
                <span x-show="isSubmitting">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                Submit
            </button>
        </div>
    </form>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('dfb-submitted', (event) => {
                // Form successfully submitted
                // Show success message or redirect
            });
            
            // Initialize the form data binding after Livewire is loaded
            document.querySelectorAll('.form-content input, .form-content textarea, .form-content select').forEach(function(element) {
                if (element.name && !element.hasAttribute('wire:model')) {
                    element.addEventListener('change', function() {
                        if (element.type === 'checkbox') {
                            @this.$set('formData', element.name, element.checked ? 1 : 0);
                        } else if (element.type === 'radio') {
                            if (element.checked) {
                                @this.$set('formData', element.name, element.value);
                            }
                        } else {
                            @this.$set('formData', element.name, element.value);
                        }
                    });
                    
                    // Add input event for real-time updating
                    if (element.type !== 'checkbox' && element.type !== 'radio' && element.type !== 'file') {
                        element.addEventListener('input', function() {
                            @this.$set('formData', element.name, element.value);
                        });
                    }
                }
            });
        });
        
        // Add custom interaction for rating options
        document.addEventListener('DOMContentLoaded', function() {
            // Style rating options
            document.querySelectorAll('.rating-options').forEach(function(ratingGroup) {
                const labels = ratingGroup.querySelectorAll('label');
                const inputs = ratingGroup.querySelectorAll('input[type="radio"]');
                
                inputs.forEach(function(input) {
                    input.addEventListener('change', function() {
                        // Reset all labels
                        labels.forEach(label => {
                            label.classList.remove('bg-indigo-500', 'text-white');
                            label.classList.add('bg-white', 'text-gray-700');
                        });
                        
                        // Style selected label
                        if (this.checked) {
                            const label = document.querySelector(`label[for="${this.id}"]`);
                            if (label) {
                                label.classList.remove('bg-white', 'text-gray-700');
                                label.classList.add('bg-indigo-500', 'text-white');
                            }
                            
                            // Update Livewire model
                            @this.$set('formData', this.name, this.value);
                        }
                    });
                    
                    // Initialize on load
                    if (input.checked) {
                        const label = document.querySelector(`label[for="${input.id}"]`);
                        if (label) {
                            label.classList.remove('bg-white', 'text-gray-700');
                            label.classList.add('bg-indigo-500', 'text-white');
                        }
                    }
                });
            });
        });
    </script>
</div>
