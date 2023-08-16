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
            }

            if (count($oneRow) > 0) {
                self::$buttons[] = $oneRow;
            }
        }
    }

    /**
     * @param string $text
     * @param array $callback
     */
    static function addButton(
        string $text,
        array  $callback,
    ): void
    {
        self::$buttons[] = [[
            'text' => $text,
            'callback_data' => json_encode($callback),
        ]];
    }

    /**
     * @param string $text
     */
    static function addInlineButton(
        string $text,
    ): void
    {
        self::$buttons[] = [[
            'text' => $text,
            'switch_inline_query_current_chat' => '',
        ]];
    }

    /**
     * @param array $buttons
     */
    static function addButtons(array $buttons): void
    {
        self::$buttons[] = array_map(function ($button) {
            return [
                'text' => $button['text'],
                'callback_data' => json_encode($button['callback_data']),
            ];
        }, $buttons);
    }

    static function get(): array
    {
        return self::$buttons;
    }
}
