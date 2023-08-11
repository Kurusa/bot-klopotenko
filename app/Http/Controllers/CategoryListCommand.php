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

        if ($this->update->getCallbackQuery()) {
            $this->getBot()->editMessageWithInlineKeyboard(
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                config('texts')['category_list'],
                $this->buildRecipeCategoriesListButtons($categories),
            );
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['category_list'],
                new InlineKeyboardMarkup($this->buildRecipeCategoriesListButtons($categories)),
            );
        }
    }
}
