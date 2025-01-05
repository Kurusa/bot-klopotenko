<?php

namespace App\Http\Controllers\Recipe\Save;

use App\Http\Controllers\BaseCommand;
use App\Http\Controllers\Recipe\Info\PromptRecipeInfo;

class HandleRemoveRecipeFromSaved extends BaseCommand
{
    public function handle(): void
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');
        $this->user->savedRecipes()->detach($recipeId);

        $this->triggerCommand(PromptRecipeInfo::class);
    }

    public function handleCallbackQuery(): void
    {
        $this->getBot()->answerCallbackQuery(
            $this->update->getCallbackQuery()->getId(),
            __('texts.recipe_removed_from_save'),
        );
    }
}
