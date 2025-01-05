<?php

namespace App\Enums\CallbackAction\Recipe;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Http\Controllers\Recipe\Save\HandleRemoveRecipeFromSaved;
use App\Http\Controllers\Recipe\Save\HandleSaveRecipe;

enum SavedAction: int implements CallbackActionEnum
{
    case SAVE_RECIPE = 7;
    case REMOVE_RECIPE_FROM_SAVED = 8;

    public function handler(): string
    {
        return match ($this) {
            self::SAVE_RECIPE => HandleSaveRecipe::class,
            self::REMOVE_RECIPE_FROM_SAVED => HandleRemoveRecipeFromSaved::class,
        };
    }
}
