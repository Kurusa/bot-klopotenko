<?php

namespace App\Utils\Handlers;

class RegExpCommandHandler implements CommandHandlerInterface
{
    private string $text;
    private array $regExpHandlers;

    public function __construct(string $text, array $regExpHandlers)
    {
        $this->text = $text;
        $this->regExpHandlers = $regExpHandlers;
    }

    public function handle(): ?string
    {
        foreach ($this->regExpHandlers as $pattern => $handler) {
            if (preg_match($pattern, $this->text)) {
                return $handler;
            }
        }

        return null;
    }
}
