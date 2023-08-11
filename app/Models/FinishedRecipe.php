<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinishedRecipe extends Model
{
    protected $table = 'user_finished_recipes';

    protected $fillable = [
        'user_id',
        'recipe_id',
        'rating',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
