<?php

namespace App\Listeners;

use App\Events\CookingDoneEvent;
use App\Utils\Api;
use Exception;

class CookingDoneListener
{
    /**
     * @throws Exception
     */
    public function handle(CookingDoneEvent $event): void
    {
        $bot = new Api(config('telegram.telegram_bot_token'));
        $bot->setChatId($event->user->chat_id);

        $event->user->stepToUpdate()->delete();
        $bot->deleteMessageById($event->messageId);
        $bot->sendText(config('texts')['cooking_done']);

        $event->user->finishedRecipes()->create([
            'user_id'   => $event->user->id,
            'recipe_id' => $event->recipe->id,
        ]);
    }
}
