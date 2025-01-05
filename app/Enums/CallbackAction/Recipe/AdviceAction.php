<?php

namespace App\Enums\CallbackAction\Recipe;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Http\Controllers\Recipe\Info\HandleRecipeAdvice;
use App\Http\Controllers\Recipe\Info\PromptRecipeInfo;

enum AdviceAction: int implements CallbackActionEnum
{
    case SHOW_ADVICE = 5;
    case HIDE_ADVICE = 6;

    public function handler(): string
    {
        return match ($this) {
            self::SHOW_ADVICE => HandleRecipeAdvice::class,
            self::HIDE_ADVICE => PromptRecipeInfo::class,
        };
    }
}
