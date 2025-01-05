<?php

namespace App\Http\Controllers\Recipe\CookingProcess;

use App\Events\CookingDoneEvent;
use App\Http\Controllers\BaseCommand;
use App\Http\Controllers\Recipe\Rate\PromptRateRecipe;
use App\Models\Recipe;

class HandleFinishCooking extends BaseCommand
{
    public function handle(): void
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));

        CookingDoneEvent::dispatch(
            $this->user,
            $this->update->getCallbackQueryMessageId(),
            $recipe,
        );
        $this->triggerCommand(PromptRateRecipe::class);
    }
}
