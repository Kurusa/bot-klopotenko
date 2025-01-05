<?php

namespace App\Services\Keyboard;

use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class MainMenuKeyboardService
{
    public static function buildKeyboard(): ReplyKeyboardMarkup
    {
        return new ReplyKeyboardMarkup([
            [__('texts.recipes_list')],
            [__('texts.saved_recipes'), __('texts.finished_recipes')],
            [__('texts.feedback')],
        ], false, true);
    }
}
