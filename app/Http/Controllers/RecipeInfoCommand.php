<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use App\Traits\RecipeInfoTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeInfoCommand extends BaseCommand
{
    use ButtonsTrait, RecipeInfoTrait;

    public function handle()
    {
        $recipe = Recipe::find($this->params['recipe_id'] ?? $this->update->getCallbackQueryByKey('recipe_id'));

        $message = '';
        $this->buildHeader($message, $recipe);
        $keyboard = $this->buildRecipeInfoButtons($recipe);

        if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'start_cooking') {
            $this->buildIngredients($message, $recipe);
            $this->buildAdvice($message, $recipe);
            $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());
            $this->getBot()->sendMessageWithKeyboard($message, new InlineKeyboardMarkup($keyboard));
        } elseif ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'hide_advice') {
            $this->buildIngredients($message, $recipe);
            $this->buildAdvice($message, $recipe);
            $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());
            $this->getBot()->sendPhoto(
                $this->user->chat_id,
                $recipe->image_url,
                $message,
                null,
                new InlineKeyboardMarkup($keyboard),
                false,
                'html',
            );
        } elseif ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'show_advice') {
            $this->buildAdvice($message, $recipe);
            $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());
            $this->getBot()->sendMessageWithKeyboard($message, new InlineKeyboardMarkup($keyboard));
        } elseif (isset($this->params['recipe_id']) && $this->update->getCallbackQuery()) {
            $this->buildAdvice($message, $recipe);
            $this->getBot()->editMessageReplyMarkup(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                new InlineKeyboardMarkup($keyboard)
            );
        } else {
            $this->buildIngredients($message, $recipe);
            $this->buildAdvice($message, $recipe);
            $this->getBot()->sendPhoto(
                $this->user->chat_id,
                $recipe->image_url,
                $message,
                null,
                new InlineKeyboardMarkup($keyboard),
                false,
                'html',
            );
        }
    }
}
