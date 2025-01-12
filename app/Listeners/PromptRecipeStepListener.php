<?php

namespace App\Listeners;

use App\Events\PromptRecipeStepEvent;
use App\Models\Step;
use App\Services\Keyboard\RecipeStep\RecipeStepKeyboardService;
use App\Utils\Api;
use Illuminate\Contracts\Queue\ShouldQueue;

class PromptRecipeStepListener implements ShouldQueue
{
    private Api $api;

    public function handle(PromptRecipeStepEvent $event): void
    {
        /** @var Api $api */
        $this->api = app(Api::class);
        $this->api->setChatId($event->user->chat_id);

        $step = $event->step;

        if (!$step->hasImage()) {
            $this->sendStepWithoutImage(
                $step,
                $event->replyToMessageId,
            );
            return;
        }

        if ($step->descriptionExceedLimit()) {
            $this->api->sendPhoto(
                $event->user->chat_id,
                $step->image_url,
            );
            $this->sendStepWithoutImage(
                $step,
                $event->replyToMessageId,
            );
        } else {
            $this->api->sendPhoto(
                $event->user->chat_id,
                $step->image_url,
                $step->description,
                $event->replyToMessageId,
                RecipeStepKeyboardService::buildKeyboard($step),
                false,
                'html',
            );
        }
    }

    private function sendStepWithoutImage(Step $step, ?int $replyToMessageId): void
    {
        $this->api->sendMessageWithKeyboard(
            $step->description,
            RecipeStepKeyboardService::buildKeyboard($step),
            null,
            $replyToMessageId,
        );
    }
}
