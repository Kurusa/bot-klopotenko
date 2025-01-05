<?php

namespace App\Listeners;

use App\Events\RateRecipeEvent;
use App\Services\Keyboard\RecipeRatingKeyboardService;
use App\Utils\Api;
use Illuminate\Contracts\Queue\ShouldQueue;

class RateRecipeListener implements ShouldQueue
{
    public function handle(RateRecipeEvent $event): void
    {
        /** @var Api $api */
        $api = app(Api::class);
        $api->setChatId($event->user->chat_id);

        $api->sendMessageWithKeyboard(
            __('texts.please_rate_recipe'),
            RecipeRatingKeyboardService::buildKeyboard($event->recipe),
        );
    }
}
