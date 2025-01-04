<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CookingSteps\StepStrategy;
use App\Http\Controllers\RecipeInfoActions\RecipeInfoActionStrategy;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\User;
use App\Utils\Api;
use App\Utils\Update;
use TelegramBot\Api\Exception;

abstract class BaseCommand
{
    protected User $user;

    public function __construct(protected Update $update)
    {
        $this->user = request()->get('user');

        $this->handleCallbackQuery();
    }

    protected function handleCallbackQuery(): void
    {
        if (!$this->update->getCallbackQuery()) {
            return;
        }

        try {
            if ($this->update->getCallbackQueryByKey('a') === 'save_recipe') {
                $this->getBot()->answerCallbackQuery(
                    $this->update->getCallbackQuery()->getId(),
                    __('texts.recipe_saved'),
                );
            } else if ($this->update->getCallbackQueryByKey('a') === 'remove_from_saved') {
                $this->getBot()->answerCallbackQuery(
                    $this->update->getCallbackQuery()->getId(),
                    __('texts.recipe_removed_from_save'),
                );
            } else {
                $this->getBot()->answerCallbackQuery($this->update->getCallbackQuery()->getId());
            }
        } catch (Exception $e) {
        }
    }

    public function getBot(): Api
    {
        return app(Api::class);
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
