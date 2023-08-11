<?php

namespace App\Utils\Handlers;

use App\Models\User;

class StatusCommandHandler implements CommandHandlerInterface
{
    private $user;
    private array $statusHandlers;

    public function __construct(?User $user, array $statusHandlers)
    {
        $this->user = $user;
        $this->statusHandlers = $statusHandlers;
    }

    public function handle(): ?string
    {
        if ($this->user && isset($this->statusHandlers[$this->user->status])) {
            return $this->statusHandlers[$this->user->status];
        }

        return null;
    }
}
