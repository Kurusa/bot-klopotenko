<?php

namespace App\Http\Controllers;

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class StartCommand extends BaseCommand
{
    public function handle()
    {
        $this->getBot()->sendMessageWithKeyboard(
            config('texts')['main_menu'] . "\n" . 'Бот в стадії розробки!',
            new ReplyKeyboardMarkup([
                [config('texts')['recipes_list']],
                [config('texts')['saved_recipes']],
                [config('texts')['finished_recipes']],
            ], false, true)
        );
    }
}
