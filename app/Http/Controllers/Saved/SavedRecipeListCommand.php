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
            $this->getBot()->sendText(config('texts')['no_saved_recipes']);
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['recipes_list'],
                new InlineKeyboardMarkup($this->buildRecipeListButtons($this->user->savedRecipes())),
            );
        }
    }
}
