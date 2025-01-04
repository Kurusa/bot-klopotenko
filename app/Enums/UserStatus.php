<?php

namespace App\Enums;

use App\Http\Controllers\Feedback;
use App\Http\Controllers\MainMenu;

enum UserStatus: string
{
    case MAIN_MENU = 'main_menu';

    case PROVIDE_FEEDBACK = 'provide_feedback';

    public function handler(): string
    {
        return match ($this) {
            self::PROVIDE_FEEDBACK => Feedback::class,
            self::MAIN_MENU => MainMenu::class,
        };
    }
}
