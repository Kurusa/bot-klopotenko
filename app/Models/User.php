<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Models\Notification\NotificationSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property bool $is_blocked
 * @property string|null $user_name
 * @property string|null $first_name
 * @property int $chat_id telegram chat ID of the user. Negative for group chats.
 * @property string $status
 */
class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'user_name',
        'first_name',
        'chat_id',
        'is_blocked',
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
