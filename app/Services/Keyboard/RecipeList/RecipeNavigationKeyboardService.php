<?php

namespace App\Services\Keyboard\RecipeList;

use Illuminate\Support\Collection;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeNavigationKeyboardService
{
    private int $limit;

    public function __construct(
        protected readonly RecipeNavigationButtonsService $recipeNavigationButtonsService,
    )
    {
        $this->limit = config('telegram.recipe_list_limit');
    }

    public function buildKeyboard(Collection $recipes, int $categoryId, int $offset): InlineKeyboardMarkup
    {
        $keyboard = RecipeButtonsService::buildButtons($recipes->skip($offset)->take($this->limit));

        $navigationButtons = [];
        if ($offset > 0) {
            $navigationButtons[] = $this->recipeNavigationButtonsService->createBackButton($offset, $categoryId);
        }

        if ($recipes->count() > $offset + $this->limit) {
            $navigationButtons[] = $this->recipeNavigationButtonsService->createNextButton($offset, $categoryId);
        }
        $keyboard[] = $navigationButtons;

        $keyboard[] = PageNumberButtonsService::createPageNumbers(
            offset: $offset,
            recipeCount: $recipes->count(),
            categoryId: $categoryId,
        );

        return new InlineKeyboardMarkup($keyboard);
    }
}
