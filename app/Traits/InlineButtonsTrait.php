<?php

namespace App\Traits;

use App\Models\Category;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;

trait InlineButtonsTrait
{
    public function buildCategoriesListInlineButtons(): array
    {
        $categories = Category::all();
        $result = [];
        foreach ($categories as $category) {
            $result[] = new Article(
                $category->id,
                $category->title,
                null, null, null, null,
                new Text($category->title),
                new InlineKeyboardMarkup($this->buildRecipeListButtons($category->recipes(), $category->id))
            );
        }

        return $result;
    }
}
