<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Api;
use App\Utils\Update;
use TelegramBot\Api\Exception;

abstract class BaseCommand
{
    protected User $user;

    public function __construct(protected Update $update)
    {
        $this->user = request()->get('user');

        $this->handleCallbackQuery();
    }

    public function handleCallbackQuery(): void
    {
        if ($this->update->getCallbackQuery()) {
            try {
                $this->getBot()->answerCallbackQuery($this->update->getCallbackQuery()->getId());
            } catch (Exception $exception) {
                $this->getBot()->notifyAdmin('BaseCommand: ' . $exception->getMessage());
            }
        }
    }

    public function getBot(): Api
    {
        return app(Api::class);
    }

    function triggerCommand($class, array $params = []): void
    {
        (new $class($this->update, $params))->handle();
    }

    abstract public function handle();
}
