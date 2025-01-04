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
            $this->getBot()->sendText(__('texts.no_finished_recipes'));
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                __('texts.recipes_list'),
                new InlineKeyboardMarkup($this->buildRecipeListButtons($this->user->finishedRecipes())),
            );
        }
    }
}
