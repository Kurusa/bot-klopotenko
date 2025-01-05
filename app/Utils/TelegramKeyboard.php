<?php

namespace App\Utils;

class TelegramKeyboard
{
    static int $columns = 1;

    static array $list;

    static array $buttons = [];

    static function build(): void
    {
        if (self::$list) {
            $oneRow = [];

            foreach (self::$list as $listKey) {
                $oneRow[] = [
                    'text' => $listKey['text'],
                    'callback_data' => json_encode($listKey['callback_data']),
                ];

                if (count($oneRow) == self::$columns) {
                    self::$buttons[] = $oneRow;
                    $oneRow = [];
                }

                if (self::$columns > 8 && count($oneRow) === 8) {
                    self::$buttons[] = $oneRow;
                    $oneRow = [];
                }
            }

            if (count($oneRow) > 0) {
                self::$buttons[] = $oneRow;
            }
        }
    }

    static function addInlineButton(string $text): void
    {
        self::$buttons[] = [[
            'text' => $text,
            'switch_inline_query_current_chat' => '',
        ]];
    }

    static function get(): array
    {
        self::$list = [];
        $buttons = self::$buttons;
        self::$buttons = [];
        return $buttons;
    }
}
