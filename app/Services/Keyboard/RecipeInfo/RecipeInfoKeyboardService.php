<?php

namespace App\Services\Keyboard\RecipeInfo;

use App\Enums\CallbackAction\BackAction;
use App\Enums\CallbackAction\CallbackAction;
use App\Enums\CallbackAction\Recipe\AdviceAction;
use App\Enums\CallbackAction\Recipe\SavedAction;
use App\Models\Recipe;
use App\Models\User;
use App\Utils\TelegramKeyboard;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeInfoKeyboardService
{
    public static function getRecipeInfoKeyboard(
        Recipe $recipe,
        bool   $showAdvice = true,
    ): InlineKeyboardMarkup
    {
        $buttons[][] = [
            'text' => __('texts.start_cooking'),
            'callback_data' => json_encode([
                'a' => CallbackAction::START_COOKING->value,
                'recipe_id' => $recipe->id,
            ])
        ];

        if ($recipe->advice) {
            self::addAdviceButtons($buttons, $showAdvice, $recipe);
        }

        self::addSavedButton($buttons, $recipe);

        /** @var User $user */
        $user = request()->get('user');
        if ($user->didRateRecipe($recipe)) {
            $buttons[][] = [
                'text' => __('texts.rate_recipe'),
                'callback_data' => json_encode([
                    'a' => CallbackAction::RATE_RECIPE->value,
                    'recipe_id' => $recipe->id,
                ]),
            ];
        }

        $buttons[][] = [
            'text' => __('texts.back'),
            'callback_data' => json_encode([
                'a' => BackAction::BACK_TO_RECIPE_LIST->value,
                'cat_id' => $recipe->category_id,
            ])
        ];

        return new InlineKeyboardMarkup($buttons);
    }

    private static function addAdviceButtons(array &$buttons, bool $showAdvice, Recipe $recipe): void
    {
        if ($showAdvice) {
            $adviceText = __('texts.show_advice');
            $adviceAction = AdviceAction::SHOW_ADVICE;
        } else {
            $adviceText = __('texts.hide_advice');
            $adviceAction = AdviceAction::HIDE_ADVICE;
        }

        $buttons[][] = [
            'text' => $adviceText,
            'callback_data' => json_encode([
                'a' => $adviceAction,
                'recipe_id' => $recipe->id,
            ]),
        ];
    }

    private static function addSavedButton(array &$buttons, Recipe $recipe): void
    {
        /** @var User $user */
        $user = request()->get('user');

        if ($user->savedRecipes()->pluck('id')->contains($recipe->id)) {
            $savedText = __('texts.remove_from_saved');
            $savedAction = SavedAction::REMOVE_RECIPE_FROM_SAVED->value;
        } else {
            $savedText = __('texts.save_for_later');
            $savedAction = SavedAction::SAVE_RECIPE->value;
        }

        $buttons[][] = [
            'text' => $savedText,
            'callback_data' => json_encode([
                'a' => $savedAction,
                'recipe_id' => $recipe->id,
            ]),
        ];
    }
}
