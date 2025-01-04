<?php

namespace App\Http\Controllers\Saved;

use App\Http\Controllers\BaseCommand;
use App\Traits\ButtonsTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class SavedRecipeListCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        if (!$this->user->savedRecipes()->count()) {
            return $this->getBot()->sendText(__('texts.no_saved_recipes'));
        }

        $this->getBot()->sendMessageWithKeyboard(
            __('texts.recipes_list'),
            new InlineKeyboardMarkup($this->buildRecipeListButtons($this->user->savedRecipes())),
        );
    }
}
