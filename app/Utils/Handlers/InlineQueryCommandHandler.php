<?php

namespace App\Utils\Handlers;

use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use App\Traits\InlineButtonsTrait;
use App\Traits\RecipeInfoTrait;
use TelegramBot\Api\Types\Inline\InlineQuery;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;

class InlineQueryCommandHandler
{
    use ButtonsTrait, InlineButtonsTrait, RecipeInfoTrait;

    public int $offset;
    public string $query;

    public function __construct(InlineQuery $inlineQuery)
    {
        $this->offset = $inlineQuery->getOffset() ? (int)$inlineQuery->getOffset() : 0;
        $this->query = $inlineQuery->getQuery();
    }

    public function handle(): array
    {
        $result = [];
        if (!$this->query) {
            $result = $this->buildCategoriesListInlineButtons();
        } else {
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
                );
            }
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
            ->take(50)
            ->get();
    }
}
