<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CookingSteps\StepStrategy;
use App\Http\Controllers\RecipeInfoActions\RecipeInfoActionStrategy;
use App\Models\Message;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use App\Utils\Api;
use App\Utils\Update;
use Exception;

abstract class BaseCommand
{
    protected Update $update;
    protected $bot;
    protected $user;
    protected array $params;

    /**
     * @param Update $update
     * @param array $params
     * @throws Exception
     */
    public function __construct(Update $update, array $params = [])
    {
        $this->params = $params;
        $this->update = $update;
        $this->loadUser();
        $this->saveMessage();
        $this->handleCallbackQuery();
    }

    protected function saveMessage()
    {
        if ($this->user->chat_id == config('telegram.admin_chat_id')) {
            return;
        }
        if ($this->update->getMessage()) {
            $payload = [
                'text' => $this->update->getMessage()->getText(),
            ];
        } elseif ($this->update->getCallbackQuery()) {
            $payload = [
                'text' => json_encode($this->update->getDecodedCallbackQueryData()),
            ];
            if ($this->update->getCallbackQueryByKey('a') === 'recipe_category') {
                $payload['category_id'] = $this->update->getCallbackQueryByKey('cat_id');
            }
        }

        if (isset($payload)) {
            $this->user->messages()->save(new Message($payload));
        }
    }

    protected function loadUser(): void
    {
        $this->user = User::where('chat_id', $this->update->getBotUser()->getId())->firstOr(function () {
            $this->user = User::create([
                'chat_id'    => $this->update->getBotUser()->getId(),
                'user_name'  => $this->update->getBotUser()->getUsername(),
                'first_name' => $this->update->getBotUser()->getFirstName(),
                'last_name'  => $this->update->getBotUser()->getLastName(),
                'status'     => 'new',
            ]);

            $this->bot->sendMessage(config('telegram.admin_chat_id'), $this->user->user_name . ' new user.');
        });
    }

    protected function handleCallbackQuery(): void
    {
        if ($this->update->getCallbackQuery()) {
            try {
                if ($this->update->getCallbackQueryByKey('a') === 'save_recipe') {
                    $this->getBot()->answerCallbackQuery(
                        $this->update->getCallbackQuery()->getId(),
                        config('texts')['recipe_saved'],
                    );
                } else if ($this->update->getCallbackQueryByKey('a') === 'remove_from_saved') {
                    $this->getBot()->answerCallbackQuery(
                        $this->update->getCallbackQuery()->getId(),
                        config('texts')['recipe_removed_from_save'],
                    );
                } else {
                    $this->getBot()->answerCallbackQuery($this->update->getCallbackQuery()->getId());
                }
            } catch (\TelegramBot\Api\Exception $e) {
            }
        }
    }

    public function getBot(): Api
    {
        if (!$this->bot) {
            $this->bot = new Api(config('telegram.telegram_bot_token'));
            $this->bot->setChatId($this->update->getBotUser()->getId());
        }

        return $this->bot;
    }

    function triggerCommand($class, array $params = []): void
    {
        (new $class($this->update, $params))->handle();
    }

    protected function createStrategy(string $strategyClass): StepStrategy|RecipeInfoActionStrategy
    {
        $strategy = new $strategyClass();
        $strategy->setContext($this->update, $this->user, $this->getBot());
        return $strategy;
    }

    protected function performStepAction(StepStrategy $strategy, Step $step): void
    {
        $strategy->performStepAction($step);
    }

    protected function performRecipeInfoAction(RecipeInfoActionStrategy $strategy, Recipe $recipe, string $message): void
    {
        $strategy->performStepAction($recipe, $message);
    }

    abstract function handle();
}
