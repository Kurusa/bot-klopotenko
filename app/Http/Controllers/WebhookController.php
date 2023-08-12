<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use App\Traits\RecipeInfoTrait;
use App\Utils\Api;
use App\Utils\FindCommandHandler;
use App\Utils\Handlers\InlineQueryCommandHandler;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\Update;

class WebhookController
{
    use ButtonsTrait, RecipeInfoTrait;

    public function handle(): void
    {
        $client = new Client(getenv('TELEGRAM_BOT_TOKEN'));

        $client->on(function (Update $update) {
            $newUpdate = new \App\Utils\Update($update);

            $this->createHandlerInstance($this->determineHandler($newUpdate), $newUpdate)->handle();
        }, function (Update $update) {
            return ($update->getMessage() !== null && !$update->getMessage()->getViaBot()) || $update->getCallbackQuery() !== null;
        });

        $client->on(function (Update $update) {
            $bot = new Api(config('telegram.telegram_bot_token'));
            $handler = new InlineQueryCommandHandler($update->getInlineQuery());
            try {
                $bot->answerInlineQuery(
                    $update->getInlineQuery()->getId(),
                    $handler->handle(),
                    0,
                    false,
                    $handler->offset + 50
                );
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }

            return true;
        }, function (Update $update) {
            return $update->getInlineQuery() !== null;
        });

        $client->run();
    }

    private function determineHandler(Update $update): ?string
    {
        $handlerClassName = null;
        if ($update->getCallbackQuery()) {
            $action = \json_decode($update->getCallbackQuery()->getData(), true)['a'];

            if (isset(config('telegram.handlers.callback')[$action])) {
                $handlerClassName = config('telegram.handlers.callback')[$action];
            }
        } elseif ($update->getMessage()->getText()) {
            $handler = new FindCommandHandler($update);
            $handlerClassName = $handler->findCommandHandler();
        }

        return $handlerClassName;
    }

    private function createHandlerInstance(?string $handlerClassName, Update $update)
    {
        return new ($handlerClassName ?? StartCommand::class) ($update);
    }

    public function test()
    {
        $recipes = Recipe::first();
        dd($recipes->ingredient_list);
    }
}
