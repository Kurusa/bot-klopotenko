<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Traits\HasKeyboard;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Feedback extends BaseCommand
{
    use HasKeyboard;

    public function handle(): void
    {
        if ($this->user->matchStatus(UserStatus::PROVIDE_FEEDBACK)) {
            $this->user->feedbacks()->create(['message' => $this->update->getMessageText()]);

            $this->getBot()->sendText(__('texts.message_sent'));

            $this->triggerCommand(MainMenu::class);
        } else {
            $this->user->setStatus(UserStatus::PROVIDE_FEEDBACK);

            $buttons = [];

            $this->getBot()->sendMessageWithKeyboard(
                __('texts.pre_send_feedback'),
                new ReplyKeyboardMarkup(self::addBackButton($buttons), false, true)
            );
        }
    }
}
