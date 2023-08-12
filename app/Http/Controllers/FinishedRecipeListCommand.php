<?php

namespace App\Http\Controllers;

use App\Traits\ButtonsTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class FinishedRecipeListCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        if (!$this->user->finishedRecipes()->count()) {
            $this->getBot()->sendText(config('texts')['no_finished_recipes']);
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['recipes_list'],
                new InlineKeyboardMarkup($this->buildRecipeListButtons($this->user->finishedRecipes())),
            );
        }
    }
}
