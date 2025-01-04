<?php

namespace App\Http\Controllers;

class Back extends BaseCommand
{
    public function handle(): void
    {
        switch ($this->user->status) {
            default:
                $this->triggerCommand(MainMenu::class);
                break;
        }
    }
}
