<?php

namespace App\Services\Keyboard\RecipeList;

use App\Enums\CallbackAction\CallbackAction;

class RecipeNavigationButtonsService
{
    private int $limit;

    public function __construct()
    {
        $this->limit = config('telegram.recipe_list_limit');
    }

    public function createBackButton(int $offset, int $categoryId = null): array
    {
        return [
            'text' => '<',
            'callback_data' => json_encode([
                'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                'offset' => $offset - $this->limit,
                'cat_id' => $categoryId,
            ])
        ];
    }

    public function createNextButton(int $offset, int $categoryId = null): array
    {
        return [
            'text' => '>',
            'callback_data' => json_encode([
                'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                'offset' => $offset + $this->limit,
                'cat_id' => $categoryId,
            ]),
        ];
    }
}
