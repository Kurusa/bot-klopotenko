<?php

namespace App\Services\Keyboard\RecipeList;

use App\Enums\CallbackAction\CallbackAction;
use App\Models\Recipe;
use Illuminate\Support\Collection;

class RecipeButtonsService
{
    public static function buildButtons(Collection $recipes)
    {
        return $recipes
            ->chunk(1)
            ->map(function ($chunk) {
                return $chunk->map(function (Recipe $recipe) {
                    return [
                        'text' => view('recipes.partials.title', ['recipe' => $recipe])->render(),
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
