<?php

namespace App\Http\Controllers\Recipe\Save;

use App\Http\Controllers\BaseCommand;
use App\Services\Keyboard\RecipeList\SavedRecipeListKeyboard;

class PromptSavedRecipeList extends BaseCommand
{
    public function handle(): void
    {
        if (!$this->user->savedRecipes()->count()) {
            $this->getBot()->sendText(__('texts.no_saved_recipes'));
            return;
        }

        /** @var SavedRecipeListKeyboard $keyboardService */
        $keyboardService = app(SavedRecipeListKeyboard::class);
        $this->getBot()->sendMessageWithKeyboard(
            __('texts.recipes_list'),
            $keyboardService->buildKeyboard($this->user->savedRecipes),
        );
    }
}
