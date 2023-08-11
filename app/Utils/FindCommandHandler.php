<?php

namespace App\Utils;

use App\Models\User;
use App\Utils\Handlers\KeyboardCommandHandler;
use App\Utils\Handlers\RegExpCommandHandler;
use App\Utils\Handlers\SlashCommandHandler;
use App\Utils\Handlers\StatusCommandHandler;

class FindCommandHandler
{
    private string $text;
    private array $handlers;
    private $user;

    public function __construct($update)
    {
        $this->user = User::where('chat_id', $update->getBotUser()->getId())->first();
        $this->text = $update->getMessage()->getText();
        $this->handlers = config('telegram.handlers');
    }

    public function findCommandHandler(): string|array|null
    {
        return (new StatusCommandHandler($this->user, $this->handlers['status']))
                ->handle() ??
            (new SlashCommandHandler($this->text, $this->handlers['slash']))
                ->handle() ??
            (new KeyboardCommandHandler($this->text, $this->handlers['keyboard']))
                ->handle() ??
            (new RegExpCommandHandler($this->text, $this->handlers['reg_exp']))
                ->handle();
    }
}
