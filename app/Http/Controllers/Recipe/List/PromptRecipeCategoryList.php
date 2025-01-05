<?php

namespace App\Http\Controllers\Recipe\List;

use App\Http\Controllers\BaseCommand;
use App\Services\Keyboard\RecipeList\RecipeCategoryListKeyboardService;

class PromptRecipeCategoryList extends BaseCommand
{
    public function handle(): void
    {
        $this->getBot()->sendMessageWithKeyboard(
            __('texts.category_list'),
            RecipeCategoryListKeyboardService::buildKeyboard(),
            $this->update->getCallbackQueryMessageId(),
        );
    }
}
