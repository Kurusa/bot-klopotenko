<?php

namespace App\Http\Controllers\Saved;

use App\Http\Controllers\BaseCommand;
use App\Http\Controllers\RecipeInfoCommand;
use App\Traits\ButtonsTrait;

class SaveRecipeCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');
        $this->user->savedRecipes()->attach($recipeId);

        $this->getBot()->sendText(config('texts')['recipe_saved']);

        $this->triggerCommand(RecipeInfoCommand::class, ['recipe_id' => $recipeId]);
    }
}
