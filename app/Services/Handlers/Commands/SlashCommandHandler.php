<?php

namespace App\Services\Handlers\Commands;

class SlashCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ?string        $text,
        private readonly array $slashHandlers,
    )
    {
        $this->text = $this->sanitizeCommand($this->text);
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

    private function sanitizeCommand(?string $text): ?string
    {
        return str_replace('@synoptic_bot', '', $text);
    }
}
