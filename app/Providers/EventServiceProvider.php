<?php

namespace App\Providers;

use App\Events\CookingDoneEvent;
use App\Events\CreatedNewRecipeEvent;
use App\Listeners\CookingDoneListener;
use App\Listeners\CreatedNewRecipeListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CookingDoneEvent::class => [
            CookingDoneListener::class,
        ],
        CreatedNewRecipeEvent::class => [
            CreatedNewRecipeListener::class,
        ],
    ];
}
