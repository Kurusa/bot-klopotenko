<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepToUpdate extends Model
{
    protected $table = 'step_to_update';

    protected $fillable = [
        'step_id',
        'recipe_id',
        'user_id',
        'next_value',
        'message_id',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
