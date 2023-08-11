<?php

namespace App\Utils\Handlers;

class KeyboardCommandHandler implements CommandHandlerInterface
{
    private string $text;
    private array $keyboardHandlers;

    public function __construct(string $text, array $keyboardHandlers)
    {
        $this->text = $text;
        $this->keyboardHandlers = $keyboardHandlers;
    }

    public function handle(): ?string
    {
        $key = $this->processKeyboardCommand($this->text);
        if ($key) {
            if (isset($this->keyboardHandlers[$key])) {
                return $this->keyboardHandlers[$key];
            }
        }

        return null;
    }

    private function processKeyboardCommand(string $text): ?string
    {
        $translations = \array_flip(config('texts'));
        if (isset($translations[$text])) {
            return $translations[$text];
        }

        return null;
    }
}
