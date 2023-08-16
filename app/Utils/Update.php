<?php

namespace App\Utils;

use Exception;
use TelegramBot\Api\Types\User;

class Update extends \TelegramBot\Api\Types\Update
{
    private array $decodedCallbackQueryData = [];

    public function __construct(\TelegramBot\Api\Types\Update $update)
    {
        if ($update->getCallbackQuery()) {
            parent::setCallbackQuery($update->getCallbackQuery());
        }

        if ($update->getMessage()) {
            parent::setMessage($update->getMessage());
        }

        if ($update->getInlineQuery()) {
            parent::setInlineQuery($update->getInlineQuery());
        }

        if ($update->getChosenInlineResult()) {
            parent::setChosenInlineResult($update->getChosenInlineResult());
        }
    }

    public function getBotUser(): User
    {
        if ($this->getCallbackQuery()) {
            $user = $this->getCallbackQuery()->getFrom();
        } elseif ($this->getMessage()) {
            $user = $this->getMessage()->getFrom();
        } elseif ($this->getInlineQuery()) {
            $user = $this->getInlineQuery()->getFrom();
        } else {
            throw new Exception('cant get telegram user data');
        }

        return $user;
    }

    public function getDecodedCallbackQueryData(): array
    {
        if ($this->getCallbackQuery() && !$this->decodedCallbackQueryData) {
            $this->decodedCallbackQueryData = json_decode($this->getCallbackQuery()->getData(), true);
        }

        return $this->decodedCallbackQueryData;
    }

    public function getCallbackQueryByKey(string $key, $default = '')
    {
        return isset($this->getDecodedCallbackQueryData()[$key]) ? $this->getDecodedCallbackQueryData()[$key] : $default;
    }
}
