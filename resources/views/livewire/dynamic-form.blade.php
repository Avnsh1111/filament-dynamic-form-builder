<div>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}
        <div class="pt-2">
            <x-filament::button type="submit">
                Submit
            </x-filament::button>
        </div>
    </form>
</div>
