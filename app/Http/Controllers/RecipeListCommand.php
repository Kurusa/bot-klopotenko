<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Traits\ButtonsTrait;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class RecipeListCommand extends BaseCommand
{
    use ButtonsTrait;

    public function handle()
    {
        $recipes = Recipe::where('category_id', $this->params['cat_id'] ?? $this->update->getCallbackQueryByKey('cat_id'));

        if ($this->update->getCallbackQuery()) {
            if ($this->update->getCallbackQuery()->getMessage()) {
                $this->getBot()->editMessageWithInlineKeyboard(
                    $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                    config('texts')['recipes_list'],
                    $this->buildRecipeListButtons($recipes),
                );
            } else {
                Log::info($this->update->toJson());
                $this->getBot()->editMessageReplyMarkup(
                    null,
                    null,
                    new InlineKeyboardMarkup($this->buildRecipeListButtons($recipes)),
                    $this->update->getCallbackQuery()->getInlineMessageId(),
                );
            }
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                config('texts')['recipes_list'],
                new InlineKeyboardMarkup($this->buildRecipeListButtons($recipes)),
            );
        }
    }
}
