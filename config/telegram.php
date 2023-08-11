<?php

use App\Http\Controllers\CategoryListCommand;
use App\Http\Controllers\RecipeInfoCommand;
use App\Http\Controllers\RecipeListCommand;
use App\Http\Controllers\RemoveRecipeFromSavedCommand;
use App\Http\Controllers\SavedRecipeListCommand;
use App\Http\Controllers\SaveRecipeCommand;
use App\Http\Controllers\StartCommand;
use App\Http\Controllers\StartCookingCommand;

return [
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),

    'handlers' => [
        'callback' => [
            'recipe_category'   => RecipeListCommand::class,
            'recipe_info'       => RecipeInfoCommand::class,
            'save_recipe'       => SaveRecipeCommand::class,
            'remove_from_saved' => RemoveRecipeFromSavedCommand::class,
            'show_advice'       => RecipeInfoCommand::class,
            'hide_advice'       => RecipeInfoCommand::class,
            'start_cooking'     => StartCookingCommand::class,
            'next_step'         => StartCookingCommand::class,
            'start_timer'       => StartCookingCommand::class,
            'skip_timer'        => StartCookingCommand::class,
            'next'              => RecipeListCommand::class,
            'back'              => RecipeListCommand::class,
        ],

        'status' => [],

        'reg_exp' => [
        ],

        'keyboard' => [
            'recipes_list' => CategoryListCommand::class,
            'saved_recipes' => SavedRecipeListCommand::class,
        ],

        'slash' => [
            '/start'             => StartCommand::class,
            '/recipes'           => RecipeListCommand::class,
        ],
    ],
];
