<?php

namespace App\Services\Keyboard\RecipeList;

use App\Enums\CallbackAction\CallbackAction;
use App\Models\Category;
use App\Utils\TelegramKeyboard;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeCategoryListKeyboardService
{
    public static function buildKeyboard(): InlineKeyboardMarkup
    {
        $categories = Category::select('categories.id', 'categories.title')
            ->join('recipes', 'categories.id', '=', 'recipes.category_id')
            ->groupBy('categories.id', 'categories.title')
            ->get();

        TelegramKeyboard::addInlineButton(__('texts.search'));

        $buttons = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $buttons[] = [
                'text' => $category->titleWithRecipesCount(),
                'callback_data' => [
                    'a' => CallbackAction::SELECT_RECIPE_CATEGORY->value,
                    'cat_id' => $category->id,
                ],
            ];
        }

        TelegramKeyboard::$columns = 2;
        TelegramKeyboard::$list = $buttons;
        TelegramKeyboard::build();

        return new InlineKeyboardMarkup(TelegramKeyboard::get());
    }
}
