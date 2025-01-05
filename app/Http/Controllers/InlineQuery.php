<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\Keyboard\RecipeInfo\RecipeInfoKeyboardService;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;

class InlineQuery extends BaseCommand
{
    public function handle(): void
    {
        $offset = (int)$this->update->getInlineQuery()->getOffset() ?? 0;
        $query = $this->update->getInlineQuery()->getQuery();

        $result = [];
        $recipes = $this->getRecipes($query, $offset);

        /** @var Recipe $recipe */
        foreach ($recipes as $recipe) {
            $message = view('recipes.partials.title', ['recipe' => $recipe])->render();
            $message .= $recipe->ingredient_list;

            $result[] = new Article(
                $recipe->id,
                $recipe->title,
                view('recipes.partials.description', ['recipe' => $recipe])->render(),
                $recipe->image_url,
                null, null,
                new Text($message, 'html'),
                RecipeInfoKeyboardService::buildKeyboard($recipe),
            );
        }

        $this->getBot()->answerInlineQuery(
            $this->update->getInlineQuery()->getId(),
            $result,
            1
        );
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
