<?php

namespace App\Services\Keyboard\RecipeList;

use App\Enums\CallbackAction\CallbackAction;
use App\Models\Recipe;
use App\Traits\HasKeyboard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeNavigationKeyboardService
{
    use HasKeyboard;

    private const LIMIT = 15;
    private const ROWS = 2;

    public function __construct(
        protected readonly RecipeNavigationButtonsService $recipeNavigationButtonsService,
    )
    {
    }

    public function buildKeyboard(Collection $recipes, int $categoryId, int $offset): InlineKeyboardMarkup
    {
        $keyboard = $this->generateRecipeButtons($recipes->skip($offset)->take(self::LIMIT));

        $navigationButtons = [];
        if ($offset > 0) {
            $navigationButtons[] = $this->recipeNavigationButtonsService->createBackButton($categoryId, $offset, self::LIMIT);
        }

        if ($recipes->count() > $offset + self::LIMIT) {
            $navigationButtons[] = $this->recipeNavigationButtonsService->createNextButton($categoryId, $offset, self::LIMIT);
        }
        $keyboard[] = $navigationButtons;

        $keyboard[] = PageNumberButtonsService::createPageNumbers(
            categoryId: $categoryId,
            offset: $offset,
            recipeCount: $recipes->count(),
            limit: self::LIMIT,
        );

        return new InlineKeyboardMarkup($keyboard);
    }

    private function generateRecipeButtons(Collection $recipes)
    {
        return $recipes
            ->chunk(1)
            ->map(function ($chunk) {
                return $chunk->map(function (Recipe $recipe) {
                    return [
                        'text' => $recipe->title,
                        'callback_data' => json_encode([
                            'a' => CallbackAction::SELECT_RECIPE_TO_SEE_INFO->value,
                            'recipe_id' => $recipe->id,
                        ]),
                    ];
                })
                    ->values()
                    ->toArray();
            })
            ->values()
            ->toArray();
    }
}
