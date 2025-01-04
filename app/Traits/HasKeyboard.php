<?php

namespace App\Traits;

trait HasKeyboard
{
    public static function addBackButton(&$buttons): array
    {
        $buttons[] = [__('texts.back')];

        return $buttons;
    }
}
