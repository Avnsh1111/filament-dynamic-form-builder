@extends('filament-dfb::layouts.public')

@section('content')
    <div class="breadcrumbs">
        <a href="{{ url('/') }}">Forms</a>
        <span class="breadcrumbs-separator">›</span>
        <span>{{ $form->name }}</span>
    </div>

    <div class="form-section">
        <div class="form-section-content">
            <h1 class="text-2xl font-semibold mb-2">{{ $form->name }}</h1>
            @if($form->description)
                <p class="text-gray-600 mb-6">{{ $form->description }}</p>
            @endif

            @livewire('av-dynamic-form', ['slug' => $form->slug])
        </div>
    </div>
@endsection
