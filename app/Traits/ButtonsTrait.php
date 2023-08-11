<?php

namespace App\Traits;

use App\Models\Recipe;
use App\Models\Step;
use Illuminate\Support\Collection;

trait ButtonsTrait
{
    public function buildRecipeListButtons($recipes, $catId = 0): array
    {
        $limit = 10;
        $offset = isset($this->update) ? $this->update->getCallbackQueryByKey('offset', 0) : 0;
        $offsetedRecipes = $recipes->skip($offset)->take($limit)->get(['id', 'title', 'complexity', 'category_id']);

        $buttons = [];
        foreach ($offsetedRecipes as $recipe) {
            $buttons[][] = [
                'text' => $recipe->title . config('constants')['complexity_data'][$recipe->complexity]['emoji'],
                'callback_data' => json_encode([
                    'a' => 'recipe_info',
                    'recipe_id' => $recipe->id,
                ]),
            ];
        }

        if ($offset > 0) {
            $buttons[] = [[
                'text' => '<',
                'callback_data' => json_encode([
                    'a'      => 'back',
                    'offset' => $offset - 10,
                    'cat_id' => isset($this->update) ? $this->update->getCallbackQueryByKey('cat_id') : $catId,
                ]),
            ]];
        }

        if (!empty($recipes->offset($offset + 10)->value('id'))) {
            $buttons[] = [[
                'text' => '>',
                'callback_data' => json_encode([
                    'a' => 'next',
                    'offset' => $offset + 10,
                    'cat_id' => isset($this->update) ? $this->update->getCallbackQueryByKey('cat_id') : $catId,
                ]),
            ]];
        }

        return $buttons;
    }

    public function buildRecipeCategoriesListButtons(Collection $categories): array
    {
        $buttons = [];
        $count = 0;
        foreach ($categories as $key => $category) {
            if ($key !== 0 && $key % 2 === 0) {
                $count++;
            }

            $buttons[$count][] = [
                'text' => $category->title . ' (' . $category->recipes()->count() . ')',
                'callback_data' => json_encode([
                    'a'      => 'recipe_category',
                    'cat_id' => $category->id,
                ]),
            ];
        }

        $buttons[][] = [
            'text' => config('texts')['all_recipes'] . ' (' . Recipe::count() . ')',
            'callback_data' => json_encode([
                'a'      => 'recipe_category',
                'cat_id' => $category->id,
            ]),
        ];

        return $buttons;
    }

    public function buildRecipeInfoButtons(Recipe $recipe): array
    {
        $keyboard = [[[
            'text' => config('texts')['start_cooking'],
            'callback_data' => json_encode([
                'a' => 'start_cooking',
                'recipe_id' => $recipe->id,
            ]),
        ]]];

        if ($recipe->advice) {
            if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') === 'show_advice') {
                $text = config('texts')['hide_advice'];
                $action = 'hide_advice';
            } else {
                $text = config('texts')['show_advice'];
                $action = 'show_advice';
            }

            $keyboard[] = [[
                'text' => $text,
                'callback_data' => json_encode([
                    'a' => $action,
                    'recipe_id' => $recipe->id,
                ]),
            ]];
        }

        if ($this->user->savedRecipes()->pluck('id')->contains($recipe->id)) {
            $text = config('texts')['remove_from_saved'];
            $action = 'remove_from_saved';
        } else {
            $text = config('texts')['save_for_later'];
            $action = 'save_recipe';
        }

        $keyboard[] = [[
            'text' => $text,
            'callback_data' => json_encode([
                'a' => $action,
                'recipe_id' => $recipe->id,
            ]),
        ]];

        return $keyboard;
    }

    public function buildTimerButtons(Step $step): array
    {
        return [
            [[
                'text' => '⏲️ ' . $step->time,
                'callback_data' => json_encode([]),
            ]],
            [[
                'text' => config('texts')['skip_timer'],
                'callback_data' => json_encode([
                    'a' => 'skip_timer',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id + 1,
                ]),
            ]],
        ];
    }

    public function buildRecipeStepButtons(Step $step): array
    {
        if ($step->time) {
            return [[[
                'text' => config('texts')['start_timer'],
                'callback_data' => json_encode([
                    'a' => 'start_timer',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id,
                ]),
            ]]];
        } else {
            return [[[
                'text' => config('texts')['next_step'],
                'callback_data' => json_encode([
                    'a' => 'next_step',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id + 1,
                ]),
            ]]];
        }
    }
}
