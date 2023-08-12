<?php

namespace App\Http\Controllers\CookingSteps;

use App\Models\Step;
use App\Models\User;
use App\Traits\ButtonsTrait;
use App\Utils\Api;
use App\Utils\Update;

class NextStepStrategy implements StepStrategy
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
        $this->user->stepToUpdate()->delete();
        $this->bot->editMessageWithInlineKeyboard(
            $this->update->getCallbackQuery()->getMessage()->getMessageId(),
            $step->description,
            $this->buildRecipeStepButtons($step),
        );
    }
}
