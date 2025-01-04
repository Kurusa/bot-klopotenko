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

    public function handle(AskToRateRecipeEvent $event): void
    {
        /** @var Api $bot */
        $bot = app(Api::class);
        $bot->setChatId($event->user->chat_id);

        $bot->sendMessageWithKeyboard(
            config('texts.please_rate_recipe'),
            new InlineKeyboardMarkup($this->buildRatingsButtons($event->recipe))
        );
    }
}
