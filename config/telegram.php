<?php

use App\Http\Controllers\Back;
use App\Http\Controllers\Feedback;
use App\Http\Controllers\FinishedRecipeListCommand;
use App\Http\Controllers\RateRecipeCommand;
use App\Http\Controllers\RecipeList\PromptRecipeCategoryList;
use App\Http\Controllers\Saved\RemoveRecipeFromSavedCommand;
use App\Http\Controllers\Saved\SavedRecipeListCommand;
use App\Http\Controllers\Saved\SaveRecipeCommand;
use App\Http\Controllers\MainMenu;
use App\Http\Controllers\StartCookingCommand;
use App\Http\Controllers\TriggerAskRateRecipeCommand;

return [
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'admin_chat_id' => env('ADMIN_CHAT_ID'),

    'handlers' => [
        'callback' => [
            'save_recipe' => SaveRecipeCommand::class,
            'remove_from_saved' => RemoveRecipeFromSavedCommand::class,
            'start_cooking' => StartCookingCommand::class,
            'next_step' => StartCookingCommand::class,
            'back_step' => StartCookingCommand::class,
            'start_timer' => StartCookingCommand::class,
            'skip_timer' => StartCookingCommand::class,
            'rate' => RateRecipeCommand::class,
            'trigger_ask_rate' => TriggerAskRateRecipeCommand::class,
        ],

        'keyboard' => [
            'recipes_list' => PromptRecipeCategoryList::class,
            'saved_recipes' => SavedRecipeListCommand::class,
            'finished_recipes' => FinishedRecipeListCommand::class,
            'feedback' => Feedback::class,
            'back' => Back::class,
        ],

        'slash' => [
            '/start' => MainMenu::class,
            '/recipes' => PromptRecipeCategoryList::class,
        ],
    ],
];
