<?php

namespace App\Http\Controllers\RecipeList;

use App\Http\Controllers\BaseCommand;
use App\Services\Keyboard\RecipeList\RecipeCategoryListKeyboardService;

class PromptRecipeCategoryList extends BaseCommand
{
    public function handle(): void
    {
        $this->getBot()->sendMessageWithKeyboard(
            __('texts.category_list'),
            RecipeCategoryListKeyboardService::getRecipeCategoryListKeyboard(),
            $this->update->getCallbackQueryMessageId(),
        );
    }
}
