<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'user_name',
        'first_name',
        'last_name',
        'chat_id',
        'status',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function savedRecipes(): BelongsToMany
    {
        return $this
            ->belongsToMany(Recipe::class, 'user_saved_recipes')
            ->orderBy('user_saved_recipes.created_at', 'desc')
            ->withTimestamps();
    }

    public function stepToUpdate(): HasOne
    {
        return $this->hasOne(StepToUpdate::class);
    }

    public function finishedRecipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'user_finished_recipes')
            ->withPivot('rating')
            ->orderBy('user_finished_recipes.created_at')
            ->withTimestamps();
    }
}
