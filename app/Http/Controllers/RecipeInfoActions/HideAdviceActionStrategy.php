<?php

namespace App\Http\Controllers\RecipeInfoActions;

use App\Models\Recipe;
use App\Models\User;
use App\Traits\ButtonsTrait;
use App\Traits\RecipeInfoTrait;
use App\Utils\Api;
use App\Utils\Update;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class HideAdviceActionStrategy implements RecipeInfoActionStrategy
{
    use ButtonsTrait, RecipeInfoTrait;

    private Update $update;
    private User $user;
    private Api $bot;

    public function setContext(Update $update, User $user, Api $bot)
    {
        $this->update = $update;
        $this->user = $user;
        $this->bot = $bot;
    }

    public function performStepAction(Recipe $recipe, string $message)
    {
        $message .= $recipe->ingredient_list;

        $this->bot->deleteMessage($this->user->chat_id, $this->update->getCallbackQueryMessageId());
        $this->bot->sendPhoto(
            $this->user->chat_id,
            $recipe->image_url,
            $message,
            null,
            new InlineKeyboardMarkup($this->buildRecipeInfoButtons($recipe)),
            false,
            'html',
        );
    }
}
