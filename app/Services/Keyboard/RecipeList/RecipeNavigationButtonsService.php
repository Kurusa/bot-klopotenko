<?php

namespace App\Services\Keyboard\RecipeList;

use App\Enums\CallbackAction\CallbackAction;

class RecipeNavigationButtonsService
{
    public function createBackButton(int $categoryId, int $offset, int $limit): array
    {
        return [
            'text' => '<',
            'callback_data' => json_encode([
                'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                'offset' => $offset - $limit,
                'cat_id' => $categoryId,
            ])
        ];
    }

    public function createNextButton(int $categoryId, int $offset, int $limit): array
    {
        return [
            'text' => '>',
            'callback_data' => json_encode([
                'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                'offset' => $offset + $limit,
                'cat_id' => $categoryId,
            ]),
        ];
    }
}
