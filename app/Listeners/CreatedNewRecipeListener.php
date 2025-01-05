<?php

namespace App\Listeners;

use App\Events\CreatedNewRecipeEvent;
use App\Utils\Api;

class CreatedNewRecipeListener
{
    public function handle(CreatedNewRecipeEvent $event): void
    {
        /** @var Api $bot */
        $bot = app(Api::class);
        $bot->notifyAdmin('Створено новий рецепт: ' . $event->recipe->title);
    }
}
