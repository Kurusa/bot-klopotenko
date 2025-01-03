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
        $categories = Category::select('categories.id', 'categories.title')
            ->selectRaw('COUNT(messages.category_id) as message_count')
            ->leftJoin('messages', 'categories.id', '=', 'messages.category_id')
            ->groupBy('categories.id', 'categories.title')
            ->orderByDesc('message_count')
            ->get();
        $categoryButtons = $this->buildRecipeCategoriesListButtons($categories);

        if ($this->update->getCallbackQuery()) {
            $this->getBot()->editMessageWithInlineKeyboard(
                $this->update->getCallbackQueryMessageId(),
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
