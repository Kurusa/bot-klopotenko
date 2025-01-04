<?php


namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Models\User;
use App\Utils\Api;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoadTelegramUser
{
    public function handle(Request $request, Closure $next)
    {
        $rawUpdate = json_decode($request->getContent(), true);

        $telegramUser = $this->extractTelegramUser($rawUpdate);
        $chatId = $this->extractChatId($rawUpdate);

        if (!$telegramUser || !$chatId) {
            abort(400, 'Unable to extract Telegram user or chat ID');
        }

        $user = $this->findOrCreateUser($telegramUser, $chatId);
        $this->setupApiAndLocale($chatId, $user->language);

        $request->merge(['user' => $user]);

        return $next($request);
    }

    private function extractTelegramUser(array $rawUpdate): ?array
    {
        return $rawUpdate['callback_query']['message']['chat']
            ?? $rawUpdate['callback_query']['from']
            ?? $rawUpdate['message']['chat']
            ?? $rawUpdate['message']['from']
            ?? $rawUpdate['inline_query']['from']
            ?? null;
    }

    private function extractChatId(array $rawUpdate): ?int
    {
        return $rawUpdate['callback_query']['message']['chat']['id']
            ?? $rawUpdate['callback_query']['from']['id']
            ?? $rawUpdate['message']['chat']['id']
            ?? $rawUpdate['message']['from']['id']
            ?? $rawUpdate['inline_query']['from']['id']
            ?? null;
    }

    private function findOrCreateUser(array $telegramUser, int $chatId): User
    {
        $userName = $telegramUser['username'] ?? $telegramUser['title'] ?? '';

        return User::firstOrCreate(
            ['chat_id' => $chatId],
            [
                'user_name' => $userName,
                'first_name' => $telegramUser['first_name'] ?? null,
                'last_name' => $telegramUser['last_name'] ?? null,
                'status' => UserStatus::MAIN_MENU,
                'language' => config('app.fallback_locale'),
            ]
        );
    }

    private function setupApiAndLocale(int $chatId, string $language): void
    {
        /** @var Api $api */
        $api = app(Api::class);
        $api->setChatId($chatId);

        app()->setLocale($language);
    }
}
