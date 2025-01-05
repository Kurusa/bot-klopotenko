<?php

namespace App\Services\Keyboard;

use App\Enums\CallbackAction\Recipe\RatingAction;
use App\Models\Recipe;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeRatingKeyboardService
{
    public static function buildKeyboard(Recipe $recipe): InlineKeyboardMarkup
    {
        $buttons = [];

        foreach (RatingAction::cases() as $rating) {
            $buttons[] = [
                'text' => $rating->getName(),
                'callback_data' => json_encode([
                    'a' => $rating->value,
                    'recipe_id' => $recipe->id,
                ]),
            ];
        }

        return new InlineKeyboardMarkup([$buttons]);
    }
}
