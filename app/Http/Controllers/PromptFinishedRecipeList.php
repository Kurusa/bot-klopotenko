<?php

namespace App\Http\Controllers;

use App\Services\Keyboard\RecipeList\SavedRecipeListKeyboard;

class PromptFinishedRecipeList extends BaseCommand
{
    public function handle(): void
    {
        if (!$this->user->finishedRecipes()->count()) {
            $this->getBot()->sendText(__('texts.no_finished_recipes'));
            return;
        }

        /** @var SavedRecipeListKeyboard $keyboardService */
        $keyboardService = app(SavedRecipeListKeyboard::class);
        $this->getBot()->sendMessageWithKeyboard(
            __('texts.recipes_list'),
            $keyboardService->buildKeyboard($this->user->finishedRecipes),
        );
    }
}
