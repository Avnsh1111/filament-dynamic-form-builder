<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Http\Controllers;

use Avnsh1111\FilamentDynamicFormBuilder\Models\DynamicForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PublicFormController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $form = DynamicForm::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('filament-dfb::public.show', compact('form'));
    }

    public function submit(Request $request, string $slug)
    {
        // Submit is handled by Livewire component; this endpoint could be extended for non-Livewire usage.
        $prefix = config('filament-dfb.route_prefix', 'forms');
        return redirect("/{$prefix}/{$slug}");
    }
}
