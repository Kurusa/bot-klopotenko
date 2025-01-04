<?php

namespace App\Enums\CallbackAction\Recipe;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Http\Controllers\RecipeList\PromptRecipeInfo;

enum AdviceAction: int implements CallbackActionEnum
{
    case SHOW_ADVICE = 5;
    case HIDE_ADVICE = 6;

    public function handler(): string
    {
        return match ($this) {
            self::SHOW_ADVICE, self::HIDE_ADVICE => PromptRecipeInfo::class,
        };
    }
}
