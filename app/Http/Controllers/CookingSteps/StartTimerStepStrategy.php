<?php

namespace App\Http\Controllers\CookingSteps;

use App\Models\Step;
use App\Models\User;
use App\Traits\ButtonsTrait;
use App\Utils\Api;
use App\Utils\Update;

class StartTimerStepStrategy implements StepStrategy
{
    use ButtonsTrait;

    private Update $update;
    private User $user;
    private Api $bot;

    public function setContext(Update $update, User $user, Api $bot)
    {
        $this->update = $update;
        $this->user = $user;
        $this->bot = $bot;
    }

    public function performStepAction(Step $step)
    {
        $message = $this->bot->editMessageWithInlineKeyboard(
            $this->update->getCallbackQueryMessageId(),
            $step->description,
            $this->buildTimerButtons($step)
        );

        $step->stepToUpdate()->updateOrCreate([
            'user_id' => $this->user->id,
            'recipe_id' => $step->recipe->id,
        ], [
            'step_id' => $step->id,
            'next_value' => $step->time - 1,
            'message_id' => $message->getMessageId(),
        ]);
    }
}
