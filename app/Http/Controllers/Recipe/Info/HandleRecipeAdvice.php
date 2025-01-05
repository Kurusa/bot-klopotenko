<?php

namespace App\Http\Controllers\Recipe\Info;

use App\Enums\CallbackAction\BackAction;
use App\Http\Controllers\BaseCommand;
use App\Models\Recipe;
use App\Services\Keyboard\RecipeInfo\RecipeInfoKeyboardService;

class HandleRecipeAdvice extends BaseCommand
{
    public function handle(): void
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));

        $this->getBot()->deleteMessageById($this->update->getCallbackQueryMessageId());
        $this->getBot()->sendMessageWithKeyboard(
            __('texts.advice', ['advice' => $recipe->advice]),
            RecipeInfoKeyboardService::buildKeyboard(
                $recipe,
                false,
                BackAction::BACK_TO_RECIPE_INFO,
            ),
        );
    }
}
