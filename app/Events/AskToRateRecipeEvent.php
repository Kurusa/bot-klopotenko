<?php

namespace App\Events;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AskToRateRecipeEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;
    public Recipe $recipe;

    /**
     * @param User $user
     * @param Recipe $recipe
     */
    public function __construct(User $user, Recipe $recipe)
    {
        $this->user = $user;
        $this->recipe = $recipe;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
