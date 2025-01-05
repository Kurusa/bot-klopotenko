<?php

namespace App\Http\Controllers\Recipe\Save;

use App\Http\Controllers\BaseCommand;
use App\Http\Controllers\Recipe\Info\PromptRecipeInfo;

class HandleSaveRecipe extends BaseCommand
{
    public function handle(): void
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');

        if ($this->user->savedRecipes()->count() >= config('telegram.saved_recipes_limit')) {
            return;
        }

        $this->user->savedRecipes()->attach($recipeId);

        $this->triggerCommand(PromptRecipeInfo::class);
    }

    public function handleCallbackQuery(): void
    {
        if ($this->user->savedRecipes()->count() >= config('telegram.saved_recipes_limit')) {
            $text = __('texts.saved_recipes_list_exceeded');
        } else {
            $text = __('texts.recipe_saved');
        }

        $this->getBot()->answerCallbackQuery(
            $this->update->getCallbackQuery()->getId(),
            $text,
        );
    }
}
