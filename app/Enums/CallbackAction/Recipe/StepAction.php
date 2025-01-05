<?php

namespace App\Enums\CallbackAction\Recipe;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Http\Controllers\Recipe\CookingProcess\HandleFinishCooking;
use App\Http\Controllers\Recipe\CookingProcess\PromptRecipeStep;

enum StepAction: int implements CallbackActionEnum
{
    case FINISH_COOKING = 12;
    case NEXT_STEP = 13;
    case START_TIMER = 14;

    public function handler(): string
    {
        return match ($this) {
            self::NEXT_STEP => PromptRecipeStep::class,
            self::FINISH_COOKING => HandleFinishCooking::class,
        };
    }
}
