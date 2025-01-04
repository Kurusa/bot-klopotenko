<?php

namespace App\Services\Handlers\Updates;

use App\Http\Controllers\MainMenu;
use App\Services\Handlers\FindCommandHandler;
use App\Utils\Api;
use App\Utils\Update as CustomUpdate;
use Exception;

class TextOrCallbackQueryHandler implements UpdateHandlerInterface
{
    public function supports(CustomUpdate $update): bool
    {
        return $update->getMessage()?->getText() !== null || $update->getCallbackQuery() !== null;
    }

    public function handle(CustomUpdate $update): void
    {
        $handlerClass = $this->determineHandler($update);

        try {
            app()->make($handlerClass, ['update' => $update])->handle();
        } catch (Exception $e) {
            /** @var Api $api */
            $api = app(Api::class);
            $api->notifyAdmin($e->getMessage());
        }
    }

    private function determineHandler(CustomUpdate $update): string
    {
        $handlerClass = $update->getCallbackQuery()
            ? $update->getCallbackAction()?->handler()
            : (new FindCommandHandler($update))->findCommandHandler();

        return $handlerClass && class_exists($handlerClass)
            ? $handlerClass
            : MainMenu::class;
    }
}
