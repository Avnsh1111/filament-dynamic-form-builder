<?php

namespace Avnsh1111\FilamentDynamicFormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicFormEntry extends Model
{
    protected $table = 'dynamic_form_entries';

    protected $fillable = [
        'dynamic_form_id',
        'data',      // JSON submission payload
        'meta',      // requester ip/ua
        'user_id',
    ];

    protected $casts = [
        'data' => 'array',
        'meta' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(DynamicForm::class, 'dynamic_form_id');
    }
}
