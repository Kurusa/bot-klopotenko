<?php

namespace App\Http\Controllers\Saved;

use App\Http\Controllers\BaseCommand;
use App\Http\Controllers\RecipeInfoCommand;
use App\Traits\ButtonsTrait;

class RemoveRecipeFromSavedCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');
        $this->user->savedRecipes()->detach($recipeId);

        $this->triggerCommand(RecipeInfoCommand::class, ['recipe_id' => $recipeId]);
    }
}
