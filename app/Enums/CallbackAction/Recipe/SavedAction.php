<?php

namespace App\Enums\CallbackAction\Recipe;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Http\Controllers\RecipeList\PromptRecipeInfo;

enum SavedAction: int implements CallbackActionEnum
{
    case SAVE_RECIPE = 7;
    case REMOVE_RECIPE_FROM_SAVED = 8;

    public function handler(): string
    {
        return match ($this) {
            self::SAVE_RECIPE, self::REMOVE_RECIPE_FROM_SAVED => PromptRecipeInfo::class,
        };
    }
}
