<?php

namespace App\Listeners;

use App\Events\CookingDoneEvent;
use App\Utils\Api;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;

class CookingDoneListener implements ShouldQueue
{
    public function handle(CookingDoneEvent $event): void
    {
        /** @var Api $bot */
        $bot = app(Api::class);
        $bot->setChatId($event->user->chat_id);

        $event->user->stepToUpdate()->delete();
        $bot->sendText(__('texts.cooking_done'));

        try {
            $event->user->finishedRecipes()->attach($event->recipe->id);
        } catch (QueryException) {
        }
    }
}
