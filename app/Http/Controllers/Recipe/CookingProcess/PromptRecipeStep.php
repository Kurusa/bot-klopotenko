<?php

namespace App\Http\Controllers\Recipe\CookingProcess;

use App\Events\PromptRecipeStepEvent;
use App\Http\Controllers\BaseCommand;
use App\Models\Recipe;
use App\Models\Step;

class PromptRecipeStep extends BaseCommand
{
    public function handle(): void
    {
        $step = $this->resolveStep();

        PromptRecipeStepEvent::dispatch(
            $this->user,
            $step,
            $this->update->getCallbackQueryMessageId(),
        );
    }

    private function resolveStep(): ?Step
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));

        $stepId = $this->update->getCallbackQueryByKey('step_id');

        return $stepId ?
            $recipe->steps()->where('id', $stepId)->first() :
            $recipe->steps()->first();
    }
}
