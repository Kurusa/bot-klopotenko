<?php

namespace App\Console\Commands;

use App\Http\Controllers\RecipeInfoCommand;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use App\Utils\Api;
use App\Utils\Update;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;
use TelegramBot\Api\HttpException;

class CheckBlockedCommand extends Command
{
    protected $signature = 'check:blocked';

    public function handle()
    {
        $bot = new Api(config('telegram.telegram_bot_token'));
        foreach (User::where('is_blocked', 0)->get() as $user) {
            try {
                $bot->sendChatAction($user->chat_id, 'typing');
            } catch (HttpException) {
                $bot->sendMessage(config('telegram.admin_chat_id'), $user->user_name . ' blocked bot.');

                $user->update([
                    'is_blocked' => 1,
                ]);
            }
        }
    }
}
