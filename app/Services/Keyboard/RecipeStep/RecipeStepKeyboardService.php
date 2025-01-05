<?php

namespace App\Services\Keyboard\RecipeStep;

use App\Enums\CallbackAction\Recipe\StepAction;
use App\Models\Step;
use App\Utils\TelegramKeyboard;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeStepKeyboardService
{
    public static function buildKeyboard(Step $step): InlineKeyboardMarkup
    {
        $keyboard = [];

        /** @var Step $nextStep */
        $nextStep = Step::where([
            'index' => $step->index + 1,
            'recipe_id' => $step->recipe_id,
        ])->first();

        if ($nextStep) {
            if ($step->time) {
                $keyboard[] = [
                    'text' => __('texts.start_timer'),
                    'callback_data' => [
                        'a' => StepAction::START_TIMER->value,
                        'recipe_id' => $step->recipe_id,
                        'step_id' => $step->id,
                    ],
                ];
            } else {
                $keyboard[] = [
                    'text' => __('texts.step', ['step' => $step->index + 1]),
                    'callback_data' => [
                        'a' => StepAction::NEXT_STEP->value,
                        'recipe_id' => $step->recipe_id,
                        'step_id' => $step->id + 1,
                    ],
                ];
            }
        } else {
            $keyboard[] = [
                'text' => __('texts.finish_cooking'),
                'callback_data' => [
                    'a' => StepAction::FINISH_COOKING->value,
                    'recipe_id' => $step->recipe_id,
                ],
            ];
        }

        TelegramKeyboard::$list = $keyboard;
        TelegramKeyboard::$columns = 2;
        TelegramKeyboard::build();

        return new InlineKeyboardMarkup(TelegramKeyboard::get());
    }
}
