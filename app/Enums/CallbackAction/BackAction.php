<?php

namespace App\Enums\CallbackAction;

use App\Http\Controllers\Recipe\Info\PromptRecipeInfo;
use App\Http\Controllers\Recipe\List\PromptRecipeList;

enum BackAction: int implements CallbackActionEnum
{
    case BACK_TO_RECIPE_LIST = 10;
    case BACK_TO_RECIPE_INFO = 11;

    public function handler(): string
    {
        return match ($this) {
            self::BACK_TO_RECIPE_LIST => PromptRecipeList::class,
            self::BACK_TO_RECIPE_INFO => PromptRecipeInfo::class,
        };
    }
}
