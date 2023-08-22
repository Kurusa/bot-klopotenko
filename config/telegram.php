<?php

use App\Http\Controllers\CategoryListCommand;
use App\Http\Controllers\FeedbackCommand;
use App\Http\Controllers\FinishedRecipeListCommand;
use App\Http\Controllers\NotificationCommand;
use App\Http\Controllers\RateRecipeCommand;
use App\Http\Controllers\RecipeInfoCommand;
use App\Http\Controllers\RecipeListCommand;
use App\Http\Controllers\Saved\RemoveRecipeFromSavedCommand;
use App\Http\Controllers\Saved\SavedRecipeListCommand;
use App\Http\Controllers\Saved\SaveRecipeCommand;
use App\Http\Controllers\StartCommand;
use App\Http\Controllers\StartCookingCommand;
use App\Http\Controllers\TriggerAskRateRecipeCommand;

return [
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'admin_chat_id'      => env('ADMIN_CHAT_ID'),

    'handlers' => [
        'callback' => [
            'recipe_category'   => RecipeListCommand::class,
            'back_from_recipe'  => RecipeListCommand::class,
            'recipe_info'       => RecipeInfoCommand::class,
            'save_recipe'       => SaveRecipeCommand::class,
            'remove_from_saved' => RemoveRecipeFromSavedCommand::class,
            'show_advice'       => RecipeInfoCommand::class,
            'hide_advice'       => RecipeInfoCommand::class,
            'start_cooking'     => StartCookingCommand::class,
            'next_step'         => StartCookingCommand::class,
            'back_step'         => StartCookingCommand::class,
            'start_timer'       => StartCookingCommand::class,
            'skip_timer'        => StartCookingCommand::class,
            'rate'              => RateRecipeCommand::class,
            'trigger_ask_rate'  => TriggerAskRateRecipeCommand::class,
            'recipe_navigation' => RecipeListCommand::class,
        ],

        'status' => [
            'feedback' => FeedbackCommand::class,
        ],

        'reg_exp' => [],

        'keyboard' => [
            'recipes_list'          => CategoryListCommand::class,
            'saved_recipes'         => SavedRecipeListCommand::class,
            'finished_recipes'      => FinishedRecipeListCommand::class,
            'write_feedback'        => FeedbackCommand::class,
            'notification_settings' => NotificationCommand::class,
            'back'                  => StartCommand::class,
        ],

        'slash' => [
            '/start'   => StartCommand::class,
            '/recipes' => RecipeListCommand::class,
        ],
    ],
];
