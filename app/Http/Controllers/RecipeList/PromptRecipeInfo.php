<?php

namespace App\Http\Controllers\RecipeList;

use App\Http\Controllers\BaseCommand;
use App\Http\Controllers\RecipeInfoActions\ShowRecipeInfoActionStrategy;
use App\Models\FinishedRecipe;
use App\Models\Recipe;
use App\Services\Keyboard\RecipeInfo\RecipeInfoKeyboardService;
use App\Traits\ButtonsTrait;
use App\Traits\RecipeInfoTrait;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class PromptRecipeInfo extends BaseCommand
{
    use ButtonsTrait, RecipeInfoTrait;

    public function handle(): void
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::find($this->update->getCallbackQueryByKey('recipe_id'));

//        if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'start_cooking') {
//            $strategy = $this->createStrategy(StartCookingActionStrategy::class);
//        } elseif ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'hide_advice') {
//            $strategy = $this->createStrategy(HideAdviceActionStrategy::class);
//        } elseif ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') == 'show_advice') {
//            $strategy = $this->createStrategy(ShowAdviceActionStrategy::class);
//        } elseif (isset($this->params['recipe_id']) && $this->update->getCallbackQuery()) {
//            return $this->getBot()->editMessageReplyMarkup(
//                $this->user->chat_id,
//                $this->update->getCallbackQueryMessageId(),
//                new InlineKeyboardMarkup($this->buildRecipeInfoButtons($recipe))
//            );
//        } else {
        //}

        $message = view('recipes.show', [
            'recipe' => $recipe,
            'finishedRecipeRating' => $this->getFinishedRecipeRating($recipe),
        ])->render();

        $this->getBot()->sendPhoto(
            $this->user->chat_id,
            $recipe->image_url,
            $message,
            null,
            RecipeInfoKeyboardService::getRecipeInfoKeyboard($recipe),
            true,
            'html',
        );
    }

    private function getFinishedRecipeRating(Recipe $recipe): string
    {
        $finishedRecipe = $this->user->finishedRecipes()->where('recipe_id', $recipe->id)->first();
        if (!$finishedRecipe) {
            return '';
        }

        $message = '';

        /** @var FinishedRecipe $recipe */
        $recipe = $finishedRecipe->pivot;
        if ($recipe->rating) {
            $message .= "\n" . $recipe->ratingDescription() . "\n";
        }

        return $message;
    }
}
