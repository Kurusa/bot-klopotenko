<?php

namespace App\Http\Controllers\Recipe\CookingProcess;

use App\Events\CookingDoneEvent;
use App\Events\RateRecipeEvent;
use App\Http\Controllers\BaseCommand;
use App\Models\Recipe;

class HandleFinishCooking extends BaseCommand
{
    public function handle(): void
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));

        CookingDoneEvent::dispatch(
            $this->user,
            $recipe,
        );
        RateRecipeEvent::dispatch(
            $this->user,
            $recipe,
        );
    }
}
