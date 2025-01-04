<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class InlineQuery extends BaseCommand
{
    public function handle()
    {
        $result = [];
        $recipes = $this->getRecipes($this->query, $this->offset);
        foreach ($recipes as $recipe) {
            $message = $recipe->header;
            $message .= $recipe->ingredient_list;

            $result[] = new Article(
                $recipe->id,
                $recipe->title,
                '🍽 Порції: ' . $recipe->portions . ' | ⏱ Час: ' . $recipe->time .
                ' | ⚙ Складність: ' . $recipe->complexity_emoji . ' ' . $recipe->complexity_title,
                $recipe->image_url,
                null, null,
                new Text($message, 'html'),
                new InlineKeyboardMarkup($this->buildRecipeInfoButtons($recipe)),
            );
        }

        return $result;
    }

    private function getRecipes(string $search, int $offset)
    {
        return Recipe::where(function ($query) use ($search) {
            $query->whereHas('category', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })->orWhereHas('ingredients', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })->orWhere('title', 'like', '%' . $search . '%');
        })
            ->skip($offset)
            ->take(20)
            ->get();
    }
}
