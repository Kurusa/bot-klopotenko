<?php

namespace App\Http\Controllers\Recipe\CookingProcess;

use App\Http\Controllers\BaseCommand;
use App\Models\Recipe;
use App\Models\Step;
use App\Services\Keyboard\RecipeStep\RecipeStepKeyboardService;

class PromptRecipeStep extends BaseCommand
{
    public function handle(): void
    {
        $step = $this->resolveStep();

        if (!$step->hasImage()) {
            $this->sendStepWithoutImage($step);
            return;
        }

        if ($step->descriptionExceedLimit()) {
            $this->getBot()->sendPhoto(
                $this->user->chat_id,
                $step->image_url,
            );
            $this->sendStepWithoutImage($step);
        } else {
            $this->getBot()->sendPhoto(
                $this->user->chat_id,
                $step->image_url,
                $step->description,
                $this->update->getCallbackQueryMessageId(),
                RecipeStepKeyboardService::buildKeyboard($step),
                false,
                'html',
            );
        }
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

    private function sendStepWithoutImage(Step $step): void
    {
        $this->getBot()->sendMessageWithKeyboard(
            $step->description,
            RecipeStepKeyboardService::buildKeyboard($step),
            null,
            $this->update->getCallbackQueryMessageId(),
        );
    }
}
