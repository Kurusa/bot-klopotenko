<?php

use App\Http\Controllers\Back;
use App\Http\Controllers\Feedback;
use App\Http\Controllers\MainMenu;
use App\Http\Controllers\PromptFinishedRecipeList;
use App\Http\Controllers\Recipe\List\PromptRecipeCategoryList;
use App\Http\Controllers\Recipe\Save\PromptSavedRecipeList;

return [
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'admin_chat_id' => env('ADMIN_CHAT_ID'),
    'recipe_list_limit' => env('RECIPE_LIST_LIMIT', 15),
    'saved_recipes_limit' => env('SAVED_RECIPES_LIMIT', 15),

    'handlers' => [
        'keyboard' => [
            'recipes_list' => PromptRecipeCategoryList::class,
            'saved_recipes' => PromptSavedRecipeList::class,
            'finished_recipes' => PromptFinishedRecipeList::class,
            'feedback' => Feedback::class,
            'back' => Back::class,
        ],

        'slash' => [
            '/start' => MainMenu::class,
            '/recipes' => PromptRecipeCategoryList::class,
        ],
    ],
];
