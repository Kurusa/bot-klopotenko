<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "<b>— Крок $this->index</b>" . "\n \n" . $value,
        )->shouldCache();
    }

    public function stepToUpdate(): HasOne
    {
        return $this->hasOne(StepToUpdate::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
