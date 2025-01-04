<?php

namespace App\Enums\CallbackAction;

use App\Http\Controllers\RecipeList\PromptRecipeList;

enum BackAction: int implements CallbackActionEnum
{
    case BACK_TO_RECIPE_LIST = 10;

    public function handler(): string
    {
        return match ($this) {
            self::BACK_TO_RECIPE_LIST => PromptRecipeList::class,
        };
    }
}
