<?php

namespace App\Http\Controllers;

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class FeedbackCommand extends BaseCommand
{
    public function handle()
    {
        if ($this->user->status === 'feedback') {
            if ($this->update->getMessage()->getText() === config('texts')['back']) {
                $this->user->update([
                    'status' => 'done',
                ]);
                $this->triggerCommand(StartCommand::class);
                return;
            }

            $this->user->feedbacks()->create(['message' => $this->update->getMessage()->getText()]);

            $this->getBot()->sendText(config('texts')['message_sent']);
            $this->triggerCommand(StartCommand::class);
        } else {
            $this->user->update([
                'status' => 'feedback',
            ]);

            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['pre_send_feedback'],
                new ReplyKeyboardMarkup([
                    [config('texts')['back']]
                ])
            );
        }
    }
}
