<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeListCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $categoryId = $this->params['cat_id'] ?? $this->update->getCallbackQueryByKey('cat_id');
        $recipes = Recipe::where('category_id', $categoryId);
        $category = Category::find($categoryId);

        if ($this->update->getCallbackQuery()) {
            if ($this->update->getCallbackQueryByKey('a') === 'back_from_recipe') {
                $this->getBot()->deleteMessageById($this->update->getCallbackQuery()->getMessage()->getMessageId());
                $this->getBot()->sendMessageWithKeyboard(
                    config('texts')['recipes_with_category'] . $category->title,
                    new InlineKeyboardMarkup($this->buildRecipeListButtons($recipes)),
                );
            } elseif ($this->update->getCallbackQuery()->getMessage()) {
                $this->getBot()->editMessageWithInlineKeyboard(
                    $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                    config('texts')['recipes_with_category'] . $category->title,
                    $this->buildRecipeListButtons($recipes),
                );
            } else {
                $this->getBot()->editMessageReplyMarkup(
                    null,
                    null,
                    new InlineKeyboardMarkup($this->buildRecipeListButtons($recipes)),
                    $this->update->getCallbackQuery()->getInlineMessageId(),
                );
            }
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['recipes_with_category'] . $category->title,
                new InlineKeyboardMarkup($this->buildRecipeListButtons($recipes)),
            );
        }
    }
}
