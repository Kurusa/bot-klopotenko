<?php

namespace App\Services\Keyboard\RecipeInfo;

use App\Enums\CallbackAction\BackAction;
use App\Enums\CallbackAction\CallbackAction;
use App\Enums\CallbackAction\Recipe\AdviceAction;
use App\Enums\CallbackAction\Recipe\SavedAction;
use App\Models\Recipe;
use App\Models\User;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeInfoKeyboardService
{
    public static function buildKeyboard(
        Recipe      $recipe,
        bool        $showAdvice = true,
        ?BackAction $backAction = BackAction::BACK_TO_RECIPE_LIST,
    ): InlineKeyboardMarkup
    {
        $buttons = [];

        $buttons[][] = [
            'text' => __('texts.start_cooking'),
            'callback_data' => json_encode([
                'a' => CallbackAction::START_COOKING->value,
                'recipe_id' => $recipe->id,
            ])
        ];

        $row = [];

        if ($recipe->advice) {
            $row[] = self::getAdviceButton($showAdvice, $recipe);
        }

        $row[] = self::getSavedButton($recipe);

        if (!empty($row)) {
            $buttons[] = $row;
        }

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
                'a' => $backAction->value,
                'cat_id' => $recipe->category_id,
                'recipe_id' => $recipe->id,
            ])
        ];

        return new InlineKeyboardMarkup($buttons);
    }

    private static function getAdviceButton(bool $showAdvice, Recipe $recipe): array
    {
        $adviceText = $showAdvice ? __('texts.show_advice') : __('texts.hide_advice');
        $adviceAction = $showAdvice ? AdviceAction::SHOW_ADVICE : AdviceAction::HIDE_ADVICE;

        return [
            'text' => $adviceText,
            'callback_data' => json_encode([
                'a' => $adviceAction->value,
                'recipe_id' => $recipe->id,
            ]),
        ];
    }

    private static function getSavedButton(Recipe $recipe): array
    {
        /** @var User $user */
        $user = request()->get('user');

        $savedText = $user->savedRecipes()->pluck('id')->contains($recipe->id)
            ? __('texts.remove_from_saved')
            : __('texts.save_for_later');

        $savedAction = $user->savedRecipes()->pluck('id')->contains($recipe->id)
            ? SavedAction::REMOVE_RECIPE_FROM_SAVED
            : SavedAction::SAVE_RECIPE;

        return [
            'text' => $savedText,
            'callback_data' => json_encode([
                'a' => $savedAction->value,
                'recipe_id' => $recipe->id,
            ]),
        ];
    }
}
