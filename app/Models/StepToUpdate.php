<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $step_id
 * @property int $recipe_id
 * @property int $user_id
 * @property int $next_value
 * @property int $message_id
 *
 * @property User $user
 * @property Recipe $recipe
 */
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

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function nextStep(): ?Step
    {
        return Step::find($this->step_id + 1);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
