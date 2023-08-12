<?php

namespace App\Http\Controllers;

use App\Events\CookingDoneEvent;
use App\Http\Controllers\CookingSteps\FirstStepStrategy;
use App\Http\Controllers\CookingSteps\NextStepStrategy;
use App\Http\Controllers\CookingSteps\StartTimerStepStrategy;
use App\Models\Recipe;
use App\Traits\ButtonsTrait;

class StartCookingCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));
        $stepId = $this->update->getCallbackQueryByKey('step_id');
        if (!$stepId) {
            $step = $recipe->steps()->first();
        } else {
            $step = $recipe->steps()->where('id', $stepId)->first();
        }

        if (!$step) {
            CookingDoneEvent::dispatch(
                $this->user,
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                $recipe,
            );
            return;
        }

        if ($this->update->getCallbackQueryByKey('a') === 'start_timer') {
            $strategy = $this->createStrategy(StartTimerStepStrategy::class);
        } else if (in_array($this->update->getCallbackQueryByKey('a'), ['next_step', 'skip_timer'])) {
            $strategy = $this->createStrategy(NextStepStrategy::class);
        } else {
            $strategy = $this->createStrategy(FirstStepStrategy::class);
        }

        $this->performStepAction($strategy, $step);
    }
}
