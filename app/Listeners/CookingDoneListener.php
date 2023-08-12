<?php

namespace App\Listeners;

use App\Events\CookingDoneEvent;
use App\Utils\Api;
use Exception;
use Illuminate\Database\QueryException;

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

        try {
            $event->user->finishedRecipes()->attach($event->recipe->id);
        } catch (QueryException) {

        }
    }
}
