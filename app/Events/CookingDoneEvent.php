<?php

namespace App\Events;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CookingDoneEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;
    public $messageId;
    public Recipe $recipe;

    /**
     * @param User $user
     * @param $messageId
     * @param Recipe $recipe
     */
    public function __construct(User $user, $messageId, Recipe $recipe)
    {
        $this->user = $user;
        $this->messageId = $messageId;
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
