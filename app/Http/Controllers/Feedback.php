<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Feedback extends BaseCommand
{
    public function handle(): void
    {
        if ($this->user->matchStatus(UserStatus::PROVIDE_FEEDBACK)) {
            $this->user->feedbacks()->create(['message' => $this->update->getMessageText()]);

            $this->getBot()->sendText(__('texts.message_sent'));

            $this->triggerCommand(MainMenu::class);
        } else {
            $this->user->setStatus(UserStatus::PROVIDE_FEEDBACK);

            $this->getBot()->sendMessageWithKeyboard(
                __('texts.pre_send_feedback'),
                new ReplyKeyboardMarkup([
                    [__('texts.back')]
                ], false, true)
            );
        }
    }
}
