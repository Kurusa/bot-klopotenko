<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Utils\Api;
use Exception;
use Illuminate\Console\Command;

class CheckBlockedUsers extends Command
{
    protected $signature = 'check-blocked-users';

    public function handle(): void
    {
        $bot = new Api(config('telegram.telegram_bot_token'));

        $users = User::all();

        $this->info('Починаю перевірку');

        /** @var User $user */
        foreach ($users as $user) {
            try {
                $bot->setChatId($user->chat_id);
                $bot->sendChatAction($user->chat_id, 'typing');

                if ($user->is_blocked === true) {
                    $user->update(['is_blocked' => false]);
                    $this->alert("Користувач: {$user->id} розблокував бота");
                }
            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'Too many')) {
                    $this->info('Sleeping');
                    sleep(10);
                }

                if (str_contains($e->getMessage(), 'user is deactivated')) {
                    $this->alert("Користувач: {$user->user_name} видалений");
                    $user->delete();
                    continue;
                }

                if ($user->is_blocked === false) {
                    $user->update(['is_blocked' => true]);
                    $this->alert("Користувач: {$user->id} заблокував бота");
                }
            }
        }

        $this->info('Перевірка завершена');
    }
}
