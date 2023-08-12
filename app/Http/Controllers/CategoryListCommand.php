<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ButtonsTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class CategoryListCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $categories = Category::all();
        $categoryButtons = $this->buildRecipeCategoriesListButtons($categories);

        if ($this->update->getCallbackQuery()) {
            $this->getBot()->editMessageWithInlineKeyboard(
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                config('texts')['category_list'],
                $categoryButtons,
            );
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['category_list'],
                new InlineKeyboardMarkup($categoryButtons),
            );
        }
    }
}
