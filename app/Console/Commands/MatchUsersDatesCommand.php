<?php

namespace App\Console\Commands;

use App\Http\Controllers\RecipeInfoCommand;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use App\Utils\Update;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;

class MatchUsersDatesCommand extends Command
{
    protected $signature = 'recipe:send';

    protected function configure()
    {
        $this->setName('recipe:send')
            ->setDescription('Send a random recipe for a specific notification type')
            ->addArgument('notificationType', InputArgument::REQUIRED, 'Notification type');
    }

    public function handle()
    {
        $notificationType = $this->argument('notificationType');

        $this->sendRandomRecipe($notificationType);
    }

    private function sendRandomRecipe(string $notificationType)
    {
        $users = $this->getUsersByNotificationType($notificationType);

        /* @var $usersCollection Collection */
        foreach ($users as $notificationType => $user) {
            $categoryIds = Category::where('notification_type', $notificationType)->pluck('id');
            $recipe = Recipe::whereHas('category', function ($query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            })->inRandomOrder()->first();

            $update = new Update(new \TelegramBot\Api\Types\Update());
            $update->setUser($user);
            (new RecipeInfoCommand($update, [
                'recipe_id'       => $recipe->id,
                'is_notification' => true,
            ]))->handle();

            Log::info($notificationType . ' Send recipe to user ' . $user->id);
        }
    }

    public function getUsersByNotificationType($notificationType)
    {
        $notificationConfig = config('constants.notification_types.' . $notificationType);
        $timeRange = explode('-', $notificationConfig['time']);
        $startHour = intval($timeRange[0]);
        $endHour = intval($timeRange[1]);
        if ($endHour < $startHour) {
            return User::where(function ($query) use ($startHour, $endHour) {
                $query->whereBetween(DB::raw('HOUR(created_at)'), [$startHour, 23])
                    ->orWhereBetween(DB::raw('HOUR(created_at)'), [0, $endHour]);
            })->get();
        }

        return User::whereBetween(
            DB::raw('HOUR(created_at)'),
            [$startHour, $endHour]
        )->get();
    }
}
