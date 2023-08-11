<?php

namespace App\Utils;

use TelegramBot\Api\{BotApi, Exception, Types\Inline\InlineKeyboardMarkup, Types\Message};

class Api extends BotApi
{
    private $chatId;

    public function __construct($token, $trackerToken = null)
    {
        parent::__construct($token, $trackerToken);
    }

    public function setChatId(int $chatId)
    {
        $this->chatId = $chatId;
    }

    public function sendMessageWithKeyboard(
        string $text,
               $keyboard,
    ): Message
    {
        return parent::sendMessage(
            $this->chatId,
            $text,
            'HTML',
            true,
            null,
            $keyboard,
        );
    }

    public function editMessageWithInlineKeyboard(
        $messageId,
        string $text,
        array $keyboard,
    ): Message
    {
        return parent::editMessageText(
            $this->chatId,
            $messageId,
            $text,
            'html',
            false,
            new InlineKeyboardMarkup($keyboard),
        );
    }

    public function sendText(
        string $text,
    ): Message
    {
        return parent::sendMessage(
            $this->chatId,
            $text,
            'html',
        );
    }

    public function deleteMessageById(
        $messageId,
    )
    {
        try {
            return parent::deleteMessage(
                $this->chatId,
                $messageId,
            );
        } catch (Exception $e) {
        }
    }
}
