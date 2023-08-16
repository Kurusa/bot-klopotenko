<?php

namespace App\Traits;

use App\Models\Recipe;
use App\Models\Step;
use App\Utils\TelegramKeyboard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait ButtonsTrait
{
    public function buildRecipeListButtons($recipes, $catId = 0): array
    {
        $limit = 10;
        $offset = isset($this->update) ? $this->update->getCallbackQueryByKey('offset', 0) : 0;
        $offsetedRecipes = $recipes->skip($offset)->take($limit)->get();

        foreach ($offsetedRecipes as $recipe) {
            TelegramKeyboard::addButton($recipe->title, [
                'a' => 'recipe_info',
                'recipe_id' => $recipe->id,
            ]);
        }

        $backButton = [
            'text' => '<',
            'callback_data' => [
                'a' => 'back',
                'offset' => $offset - 10,
                'cat_id' => isset($this->update) ? $this->update->getCallbackQueryByKey('cat_id') : $catId,
            ],
        ];
        $nextButton = [
            'text' => '>',
            'callback_data' => [
                'a' => 'next',
                'offset' => $offset + 10,
                'cat_id' => isset($this->update) ? $this->update->getCallbackQueryByKey('cat_id') : $catId,
            ],
        ];
        if ($offset > 0 && !empty($recipes->offset($offset + 10)->value('id'))) {
            TelegramKeyboard::addButtons([$backButton, $nextButton]);
        } else if (!empty($recipes->offset($offset + 10)->value('id'))) {
            TelegramKeyboard::addButton($nextButton['text'], $nextButton['callback_data']);
        } else if ($offset > 0) {
            TelegramKeyboard::addButton($backButton['text'], $backButton['callback_data']);
        }

        return TelegramKeyboard::get();
    }

    public function buildRecipeCategoriesListButtons(Collection $categories): array
    {
        TelegramKeyboard::addInlineButton(config('texts')['search']);

        $buttons = [];
        foreach ($categories as $category) {
            $buttons[] = [
                'text' => $category->title . ' (' . $category->recipes()->count() . ')',
                'callback_data' => [
                    'a' => 'recipe_category',
                    'cat_id' => $category->id,
                ],
            ];
        }

        TelegramKeyboard::$columns = 2;
        TelegramKeyboard::$list = $buttons;
        TelegramKeyboard::build();

        return TelegramKeyboard::get();
    }

    public function buildRecipeInfoButtons(Recipe $recipe): array
    {
        TelegramKeyboard::addButton(config('texts')['start_cooking'], [
            'a' => 'start_cooking',
            'recipe_id' => $recipe->id,
        ]);

        $keyboard = [];
        if ($recipe->advice && !$this->update->getInlineQuery()) {
            if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') === 'show_advice') {
                $adviceText = config('texts')['hide_advice'];
                $adviceAction = 'hide_advice';
            } else {
                $adviceText = config('texts')['show_advice'];
                $adviceAction = 'show_advice';
            }

            $keyboard[] = [
                'text' => $adviceText,
                'callback_data' => [
                    'a' => $adviceAction,
                    'recipe_id' => $recipe->id,
                ],
            ];
        }

        if ($this->user->savedRecipes()->pluck('id')->contains($recipe->id)) {
            $savedText = config('texts')['remove_from_saved'];
            $savedAction = 'remove_from_saved';
        } else {
            $savedText = config('texts')['save_for_later'];
            $savedAction = 'save_recipe';
        }

        $keyboard[] = [
            'text' => $savedText,
            'callback_data' => [
                'a' => $savedAction,
                'recipe_id' => $recipe->id,
            ],
        ];
        TelegramKeyboard::$list = $keyboard;
        TelegramKeyboard::$columns = 2;
        TelegramKeyboard::build();

        if (!$this->update->getInlineQuery()) {
            if ($this->user->finishedRecipes()->pluck('recipe_id')->contains($recipe->id)) {
                TelegramKeyboard::addButton(config('texts')['rate_recipe'], [
                    'a' => 'trigger_ask_rate',
                    'recipe_id' => $recipe->id,
                ]);
            }
        }

        if (!$this->update->getInlineQuery()) {
            TelegramKeyboard::addButton(config('texts')['back'], [
                'a' => 'back_from_recipe',
                'cat_id' => $recipe->category_id,
            ]);
        }

        return TelegramKeyboard::get();
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
        $keyboard = [];

        if ($step->index > 1) {
            $keyboard[] = [
                'text' => config('texts')['back'],
                'callback_data' => [
                    'a' => 'back_step',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id - 1,
                ],
            ];
        }

        if ($step->time) {
            $keyboard[] = [
                'text' => config('texts')['start_timer'],
                'callback_data' => [
                    'a' => 'start_timer',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id,
                ],
            ];
        } else {
            $keyboard[] = [
                'text' => "— Крок " . $step->index + 1,
                'callback_data' => [
                    'a' => 'next_step',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id + 1,
                ],
            ];
        }

        TelegramKeyboard::$list = $keyboard;
        TelegramKeyboard::$columns = 2;
        TelegramKeyboard::build();

        return TelegramKeyboard::get();
    }

    public function buildRatingsButtons(Recipe $recipe): array
    {
        $buttons = [];
        foreach (config('constants')['ratings'] as $rating => $emoji) {
            $buttons[0][] = [
                'text' => $rating . $emoji,
                'callback_data' => json_encode([
                    'a' => 'rate',
                    'recipe_id' => $recipe->id,
                    'rating' => $rating,
                ]),
            ];
        }

        return $buttons;
    }
}
