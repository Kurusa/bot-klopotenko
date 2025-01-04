<?php

namespace App\Http\Controllers\RecipeList;

use App\Enums\CallbackAction\BackAction;
use App\Http\Controllers\BaseCommand;
use App\Models\Category;
use App\Services\Keyboard\RecipeList\RecipeNavigationKeyboardService;

class PromptRecipeList extends BaseCommand
{
    public function handle(): void
    {
        $categoryId = $this->update->getCallbackQueryByKey('cat_id');

        /** @var Category $category */
        $category = Category::find($categoryId);
        $recipes = $category->recipes;

        $offset = $this->update->getCallbackQueryByKey('offset', 0);

        /** @var RecipeNavigationKeyboardService $recipeNavigationKeyboardService */
        $recipeNavigationKeyboardService = app(RecipeNavigationKeyboardService::class);
        $keyboard = $recipeNavigationKeyboardService->buildKeyboard($recipes, $categoryId, $offset);

        if ($this->update->getCallbackAction() === BackAction::BACK_TO_RECIPE_LIST) {
            $this->getBot()->deleteMessageById($this->update->getCallbackQueryMessageId());
        }

        $this->getBot()->sendMessageWithKeyboard(
            __('texts.recipes_with_category', [
                'category' => $category->title,
            ]),
            $keyboard,
        );
    }
}
