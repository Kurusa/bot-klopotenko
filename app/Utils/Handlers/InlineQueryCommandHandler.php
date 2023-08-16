<?php

namespace App\Utils\Handlers;

use App\Models\Message;
use App\Models\Recipe;
use App\Models\User;
use App\Traits\ButtonsTrait;
use App\Traits\InlineButtonsTrait;
use App\Traits\RecipeInfoTrait;
use App\Utils\Update;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;

class InlineQueryCommandHandler
{
    use ButtonsTrait, InlineButtonsTrait, RecipeInfoTrait;

    public int $offset;
    public string $query;
    public $user;
    public $update;

    public function __construct(Update $update)
    {
        $this->update = $update;
        $this->offset = (int)$this->update->getInlineQuery()?->getOffset();
        $this->query = (string)$this->update->getInlineQuery()?->getQuery();
        $this->loadUser();
        $this->saveMessage();
    }

    protected function loadUser(): void
    {
        $this->user = User::where('chat_id', $this->update->getBotUser()->getId())->firstOr(function () {
            $this->user = User::create([
                'chat_id'    => $this->update->getBotUser()->getId(),
                'user_name'  => $this->update->getBotUser()->getUsername(),
                'first_name' => $this->update->getBotUser()->getFirstName(),
                'last_name'  => $this->update->getBotUser()->getLastName(),
                'status'     => 'new',
            ]);
        });
    }

    protected function saveMessage(): void
    {
        if ($this->user->chat_id == '375036391') {
            return;
        }
        $this->user->messages()->save(new Message([
            'user_id' => $this->user->id,
            'text'    => 'Inline query: ' . $this->update->getInlineQuery()->getQuery()
        ]));
    }

    public function handle(): array
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
