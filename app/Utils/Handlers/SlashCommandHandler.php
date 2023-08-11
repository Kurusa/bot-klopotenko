<?php

namespace App\Utils\Handlers;

class SlashCommandHandler implements CommandHandlerInterface
{
    private string $text;
    private array $slashHandlers;

    public function __construct(string $text, array $slashHandlers)
    {
        $this->text = $text;
        $this->slashHandlers = $slashHandlers;
    }

    public function handle(): ?string
    {
        if (str_starts_with($this->text, '/')) {
            if (isset($this->slashHandlers[$this->text])) {
                return $this->slashHandlers[$this->text];
            }
        }

        return null;
    }
}
