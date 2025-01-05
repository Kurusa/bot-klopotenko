<?php

namespace App\Http\Controllers\Recipe\Rate;

use App\Http\Controllers\BaseCommand;
use App\Models\Recipe;
use App\Services\Keyboard\RecipeRatingKeyboardService;

class PromptRateRecipe extends BaseCommand
{
    public function handle(): void
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');
        /** @var Recipe $recipe */
        $recipe = Recipe::find($recipeId);

        $this->getBot()->sendMessageWithKeyboard(
            __('texts.please_rate_recipe'),
            RecipeRatingKeyboardService::buildKeyboard($recipe),
        );
    }
}
