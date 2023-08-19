<?php

namespace App\Http\Controllers;

use App\Traits\ButtonsTrait;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class NotificationCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $this->getBot()->sendMessageWithKeyboard(
            config('texts')['notification_about'],
            new ReplyKeyboardMarkup($this->buildNotificationTypeButtons())
        );
    }
}
