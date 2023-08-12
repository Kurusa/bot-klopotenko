<?php

namespace App\Http\Controllers\RecipeInfoActions;

use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use App\Utils\Api;
use App\Utils\Update;

interface RecipeInfoActionStrategy
{
    public function setContext(Update $update, User $user, Api $bot);
    public function performStepAction(Recipe $recipe);
}
