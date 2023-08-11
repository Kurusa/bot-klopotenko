<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/' . config('telegram')['telegram_bot_token'] . '/webhook', [WebhookController::class, 'handle']);
Route::get('test', [WebhookController::class, 'test']);
