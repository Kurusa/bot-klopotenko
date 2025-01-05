<?php

namespace App\Http\Controllers\Recipe\Info;

use App\Http\Controllers\BaseCommand;
use App\Models\FinishedRecipe;
use App\Models\Recipe;
use App\Services\Keyboard\RecipeInfo\RecipeInfoKeyboardService;

class PromptRecipeInfo extends BaseCommand
{
    public function handle(): void
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));

        $message = view('recipes.show', [
            'recipe' => $recipe,
            'finishedRecipeRating' => $this->getFinishedRecipeRating($recipe),
        ])->render();

        $this->getBot()->deleteMessageById($this->update->getCallbackQueryMessageId());

        $this->getBot()->sendPhoto(
            $this->user->chat_id,
            $recipe->image_url,
            $message,
            null,
            RecipeInfoKeyboardService::buildKeyboard($recipe),
            true,
            'html',
        );
    }

    private function getFinishedRecipeRating(Recipe $recipe): string
    {
        $finishedRecipe = $this->user->finishedRecipes()->where('recipe_id', $recipe->id)->first();
        if (!$finishedRecipe) {
            return '';
        }

        $message = '';

        /** @var FinishedRecipe $recipe */
        $recipe = $finishedRecipe->pivot;
        if ($recipe->rating) {
            $message .= "\n" . $recipe->rating_description . "\n";
        }

        return $message;
    }
}
