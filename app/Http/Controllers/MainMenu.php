<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Services\Keyboard\MainMenuKeyboardService;

class MainMenu extends BaseCommand
{
    public function handle(): void
    {
        $this->user->setStatus(UserStatus::MAIN_MENU);

        if ($this->user->isGroupChat()) {
            return;
        }

        $this->getBot()->sendMessageWithKeyboard(
            __('texts.main_menu') . "\n" . '(Бот в стадії розробки)',
            MainMenuKeyboardService::buildKeyboard(),
        );
    }
}
