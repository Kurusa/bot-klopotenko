<?php

namespace App\Providers;

use App\Utils\Api;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(Api::class, function () {
            return new Api(config('telegram.telegram_bot_token'));
        });
    }
}
