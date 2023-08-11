<?php

namespace App\Utils\Handlers;

interface CommandHandlerInterface
{
    public function handle(): ?string;
}
