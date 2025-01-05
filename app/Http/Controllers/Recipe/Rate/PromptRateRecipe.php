<?php

namespace App\Http\Controllers\Recipe\Rate;

use App\Events\RateRecipeEvent;
use App\Http\Controllers\BaseCommand;
use App\Models\Recipe;

class PromptRateRecipe extends BaseCommand
{
    public function handle(): void
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');
        /** @var Recipe $recipe */
        $recipe = Recipe::find($recipeId);

        RateRecipeEvent::dispatch(
            $this->user,
            $recipe,
        );
    }
}
