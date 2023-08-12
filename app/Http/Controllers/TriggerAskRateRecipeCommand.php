<?php

namespace App\Http\Controllers;

use App\Events\AskToRateRecipeEvent;
use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use App\Traits\RecipeInfoTrait;

class TriggerAskRateRecipeCommand extends BaseCommand
{
    use ButtonsTrait, RecipeInfoTrait;

    public function handle()
    {
        AskToRateRecipeEvent::dispatch(
            $this->user,
            Recipe::find($this->update->getCallbackQueryByKey('recipe_id')),
        );
    }
}
