<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Step extends Model
{
    protected $table = 'recipe_steps';

    protected $fillable = [
        'recipe_id',
        'description',
        'time',
        'image_url',
        'step_done_text',
    ];

    public function stepToUpdate(): HasOne
    {
        return $this->hasOne(StepToUpdate::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
