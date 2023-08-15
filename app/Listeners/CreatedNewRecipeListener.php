<?php

namespace App\Listeners;

use App\Events\CreatedNewRecipeEvent;
use App\Traits\ButtonsTrait;
use App\Utils\Api;
use Exception;

class CreatedNewRecipeListener
{
    use ButtonsTrait;

    /**
     * @throws Exception
     */
    public function handle(CreatedNewRecipeEvent $event): void
    {
        $bot = new Api(config('telegram.telegram_bot_token'));
        $bot->setChatId(375036391);
        $bot->sendText('Створено новий рецепт: ' . $event->recipe->title);
    }
}
