<?php

namespace App\Providers;

use App\Events\AskToRateRecipeEvent;
use App\Events\CookingDoneEvent;
use App\Events\CreatedNewRecipeEvent;
use App\Listeners\AskToRateRecipeListener;
use App\Listeners\CookingDoneListener;
use App\Listeners\CreatedNewRecipeListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CookingDoneEvent::class => [
            CookingDoneListener::class,
        ],
        AskToRateRecipeEvent::class => [
            AskToRateRecipeListener::class,
        ],
        CreatedNewRecipeEvent::class => [
            CreatedNewRecipeListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
