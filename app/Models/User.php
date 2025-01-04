<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property bool $is_blocked
 * @property string|null $user_name
 * @property string|null $first_name
 * @property int $chat_id telegram chat ID of the user. Negative for group chats.
 * @property UserStatus $status
 * @property string $language
 *
 * @property-read Collection<Feedback> $feedbacks
 */
class User extends Model
{
    protected $fillable = [
        'user_name',
        'first_name',
        'chat_id',
        'is_blocked',
        'language',
        'status',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'status' => UserStatus::class,
    ];

    public function matchStatus(UserStatus $status): bool
    {
        return $this->status === $status;
    }

    public function setStatus(UserStatus $status): void
    {
        $this->update([
            'status' => $status,
        ]);
    }

    public function isAdmin(): bool
    {
        return $this->chat_id == config('telegram.admin_chat_id');
    }

    public function isGroupChat(): bool
    {
        return $this->chat_id < 0;
    }

    public function didRateRecipe(Recipe $recipe): bool
    {
        return $this->finishedRecipes()->pluck('recipe_id')->contains($recipe->id);
    }

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
