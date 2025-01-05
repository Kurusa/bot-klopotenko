<?php

namespace App\Services\Keyboard\RecipeList;

use Illuminate\Support\Collection;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class FinishedRecipeListKeyboard
{
    public function __construct(
        protected readonly RecipeNavigationButtonsService $recipeNavigationButtonsService,
    )
    {
    }

    public function buildKeyboard(Collection $recipes): InlineKeyboardMarkup
    {
        $keyboard = RecipeButtonsService::buildButtons($recipes);

        return new InlineKeyboardMarkup($keyboard);
    }
}
