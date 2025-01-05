<?php

namespace App\Providers;

use App\Events\CookingDoneEvent;
use App\Events\CreatedNewRecipeEvent;
use App\Events\PromptRecipeStepEvent;
use App\Events\RateRecipeEvent;
use App\Listeners\CookingDoneListener;
use App\Listeners\CreatedNewRecipeListener;
use App\Listeners\PromptRecipeStepListener;
use App\Listeners\RateRecipeListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CookingDoneEvent::class => [
            CookingDoneListener::class,
        ],
        RateRecipeEvent::class => [
            RateRecipeListener::class,
        ],
        PromptRecipeStepEvent::class => [
            PromptRecipeStepListener::class,
        ],
        CreatedNewRecipeEvent::class => [
            CreatedNewRecipeListener::class,
        ],
    ];
}
