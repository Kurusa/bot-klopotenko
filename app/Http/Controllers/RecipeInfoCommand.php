<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RecipeInfoActions\HideAdviceActionStrategy;
use App\Http\Controllers\RecipeInfoActions\ShowAdviceActionStrategy;
use App\Http\Controllers\RecipeInfoActions\ShowRecipeInfoActionStrategy;
use App\Http\Controllers\RecipeInfoActions\StartCookingActionStrategy;
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

        if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'start_cooking') {
            $strategy = $this->createStrategy(StartCookingActionStrategy::class);
        } elseif ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'hide_advice') {
            $strategy = $this->createStrategy(HideAdviceActionStrategy::class);
        } elseif ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'show_advice') {
            $strategy = $this->createStrategy(ShowAdviceActionStrategy::class);
        } elseif (isset($this->params['recipe_id']) && $this->update->getCallbackQuery()) {
            return $this->getBot()->editMessageReplyMarkup(
                $this->user->chat_id,
                $this->update->getCallbackQueryMessageId(),
                new InlineKeyboardMarkup($this->buildRecipeInfoButtons($recipe))
            );
        } else {
            $strategy = $this->createStrategy(ShowRecipeInfoActionStrategy::class);
        }

        $message = $recipe->header;
        $finishedRecipe = $this->user->finishedRecipes()->where('recipe_id', $recipe->id)->first();
        if ($finishedRecipe) {
            $rating = $finishedRecipe->pivot->rating;
            if ($rating) {
                $message .= "\n" . config('texts')['your_rating'] . $rating . config('constants')['ratings'][$rating] . "\n";
            }
        }

        $this->performRecipeInfoAction($strategy, $recipe, $message);
    }
}
