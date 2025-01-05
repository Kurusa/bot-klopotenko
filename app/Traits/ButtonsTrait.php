<?php

namespace App\Traits;

use App\Models\Recipe;
use App\Models\Step;
use App\Utils\TelegramKeyboard;

trait ButtonsTrait
{
    public function buildRecipeListButtons($recipes, $catId = 0): array
    {
        $recipesCount = $recipes->count();
        $offset = isset($this->update) ? $this->update->getCallbackQueryByKey('offset', 0) : 0;
        $offsetedRecipes = $recipes->skip($offset)->take(10)->get();

        foreach ($offsetedRecipes as $recipe) {
            TelegramKeyboard::addButton($recipe->title, [
                'a' => 'recipe_info',
                'recipe_id' => $recipe->id,
            ]);
        }

        $backButton = [
            'text' => '<',
            'callback_data' => [
                'a' => 'recipe_navigation',
                'offset' => $offset - 10,
                'cat_id' => isset($this->update) ? $this->update->getCallbackQueryByKey('cat_id') : $catId,
            ],
        ];
        $nextButton = [
            'text' => '>',
            'callback_data' => [
                'a' => 'recipe_navigation',
                'offset' => $offset + 10,
                'cat_id' => isset($this->update) ? $this->update->getCallbackQueryByKey('cat_id') : $catId,
            ],
        ];
        $this->addNavigationButtons($backButton, $nextButton, $recipes);
        $this->addPageNumbers($recipesCount, $catId);

        return TelegramKeyboard::get();
    }

    public function buildRecipeInfoButtons(Recipe $recipe): array
    {
        TelegramKeyboard::addButton(__('texts.start_cooking'), [
            'a' => 'start_cooking',
            'recipe_id' => $recipe->id,
        ]);

        $keyboard = [];
        if ($recipe->advice && !$this->update->getInlineQuery()) {
            if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') === 'show_advice') {
                $adviceText = __('texts.hide_advice');
                $adviceAction = 'hide_advice';
            } else {
                $adviceText = __('texts.show_advice');
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
            $savedText = __('texts.remove_from_saved');
            $savedAction = 'remove_from_saved';
        } else {
            $savedText = __('texts.save_for_later');
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
                TelegramKeyboard::addButton(__('texts.rate_recipe'), [
                    'a' => 'trigger_ask_rate',
                    'recipe_id' => $recipe->id,
                ]);
            }
        }

        if (!$this->update->getInlineQuery()) {
            TelegramKeyboard::addButton(__('texts.back'), [
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
                'text' => __('texts.skip_timer'),
                'callback_data' => json_encode([
                    'a' => 'skip_timer',
                    'recipe_id' => $step->recipe_id,
                    'step_id' => $step->id + 1,
                ]),
            ]],
        ];
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

    private function addNavigationButtons(array $backButton, array $nextButton, $model): void
    {
        $offset = isset($this->update) ? $this->update->getCallbackQueryByKey('offset', 0) : 0;

        if ($offset > 0 && !empty($model->offset($offset + 10)->value('id'))) {
            TelegramKeyboard::addButtons([$backButton, $nextButton]);
        } else if (!empty($model->offset($offset + 10)->value('id'))) {
            TelegramKeyboard::addButton($nextButton['text'], $nextButton['callback_data']);
        } else if ($offset > 0) {
            TelegramKeyboard::addButton($backButton['text'], $backButton['callback_data']);
        }
    }

    public function buildNotificationTypeButtons(): array
    {
        $buttons = [];
        foreach (config('constants')['notification_types'] as $notificationType) {
            $buttons[][] = $notificationType;
        }

        $buttons[][] = __('texts.back');

        return $buttons;
    }
}
