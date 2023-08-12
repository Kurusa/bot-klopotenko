<?php

namespace App\Listeners;

use App\Events\AskToRateRecipeEvent;
use App\Traits\ButtonsTrait;
use App\Utils\Api;
use Exception;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class AskToRateRecipeListener
{
    use ButtonsTrait;

    /**
     * @throws Exception
     */
    public function handle(AskToRateRecipeEvent $event): void
    {
        $bot = new Api(config('telegram.telegram_bot_token'));
        $bot->setChatId($event->user->chat_id);

        $bot->sendMessageWithKeyboard(
            config('texts')['please_rate_recipe'],
            new InlineKeyboardMarkup($this->buildRatingsButtons($event->recipe))
        );
    }
}
