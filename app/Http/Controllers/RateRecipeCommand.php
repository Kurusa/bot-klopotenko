<?php

namespace App\Http\Controllers;

use App\Traits\ButtonsTrait;
use App\Traits\RecipeInfoTrait;

class RateRecipeCommand extends BaseCommand
{
    use ButtonsTrait, RecipeInfoTrait;

    public function handle()
    {
        $this->user
            ->finishedRecipes()
            ->where('recipe_id', $this->update->getCallbackQueryByKey('recipe_id'))
            ->update(['rating' => $this->update->getCallbackQueryByKey('rating')]);

        $this->getBot()->sendText(config('texts')['thanks_for_rating']);
    }
}
