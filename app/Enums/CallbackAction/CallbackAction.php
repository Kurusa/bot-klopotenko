<?php

namespace App\Enums\CallbackAction;

use App\Http\Controllers\Recipe\CookingProcess\PromptRecipeStep;
use App\Http\Controllers\Recipe\Info\PromptRecipeInfo;
use App\Http\Controllers\Recipe\List\PromptRecipeList;
use App\Http\Controllers\Recipe\Rate\PromptRateRecipe;

enum CallbackAction: int implements CallbackActionEnum
{
    case SELECT_RECIPE_CATEGORY = 1;
    case SELECT_RECIPE_TO_SEE_INFO = 2;
    case RECIPE_LIST_NAVIGATION = 3;
    case START_COOKING = 4;
    case RATE_RECIPE = 9;

    public function handler(): string
    {
        return match ($this) {
            self::SELECT_RECIPE_CATEGORY, self::RECIPE_LIST_NAVIGATION => PromptRecipeList::class,
            self::SELECT_RECIPE_TO_SEE_INFO => PromptRecipeInfo::class,
            self::START_COOKING => PromptRecipeStep::class,
            self::RATE_RECIPE => PromptRateRecipe::class,
        };
    }
}
