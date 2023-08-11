<?php

namespace App\Http\Controllers;

use App\Traits\ButtonsTrait;

class RemoveRecipeFromSavedCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $recipeId = $this->update->getCallbackQueryByKey('recipe_id');
        $this->user->savedRecipes()->detach($recipeId);

        $this->getBot()->sendText(config('texts')['recipe_removed_from_save']);

        $this->triggerCommand(RecipeInfoCommand::class, ['recipe_id' => $recipeId]);
    }
}
