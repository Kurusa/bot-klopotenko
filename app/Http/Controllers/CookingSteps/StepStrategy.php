<?php

namespace App\Http\Controllers\CookingSteps;

use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use App\Utils\Api;
use App\Utils\Update;

interface StepStrategy
{
    public function setContext(Update $update, User $user, Api $bot);
    public function performStepAction(Recipe $recipe, Step $step);
}
