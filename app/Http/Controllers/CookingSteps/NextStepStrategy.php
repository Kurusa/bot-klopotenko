<?php

namespace App\Http\Controllers\CookingSteps;

use App\Models\Step;
use App\Models\User;
use App\Traits\ButtonsTrait;
use App\Utils\Api;
use App\Utils\Update;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

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
        if ($step->image_url) {
            $this->bot->deleteMessageById($this->update->getCallbackQuery()->getMessage()->getMessageId());
            if (strlen($step->description) >= 1024) {
                $this->bot->sendPhoto(
                    $this->user->chat_id,
                    $step->image_url,
                );
                $this->bot->sendMessageWithKeyboard(
                    $step->description,
                    new InlineKeyboardMarkup($this->buildRecipeStepButtons($step)),
                );
            } else {
                $this->bot->sendPhoto(
                    $this->user->chat_id,
                    $step->image_url,
                    $step->description,
                    null,
                    new InlineKeyboardMarkup($this->buildRecipeStepButtons($step)),
                    false,
                    'html',
                );
            }
        } else {
            $this->bot->editMessageWithInlineKeyboard(
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                $step->description,
                $this->buildRecipeStepButtons($step),
            );
        }
    }
}
