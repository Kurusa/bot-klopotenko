<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rating
 */
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

    public function ratingDescription(): string
    {
        return __('texts.your_rating') . $this->rating . config('constants.ratings.' . $this->rating);
    }
}
