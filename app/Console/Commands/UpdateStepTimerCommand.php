<?php

namespace App\Console\Commands;

use App\Events\AskToRateRecipeEvent;
use App\Events\CookingDoneEvent;
use App\Models\Step;
use App\Models\StepToUpdate;
use App\Traits\ButtonsTrait;
use App\Utils\Api;
use Illuminate\Console\Command;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class UpdateStepTimerCommand extends Command
{
    protected $signature = 'update-step-timer';
    use ButtonsTrait;

    public function handle()
    {
        $bot = new Api(config('telegram.telegram_bot_token'));
        foreach (StepToUpdate::all() as $stepToUpdate) {
            $bot->setChatId($stepToUpdate->user->chat_id);

            if ($stepToUpdate->next_value === 0) {
                $nextStep = Step::find($stepToUpdate->step_id + 1);
                $bot->deleteMessage($stepToUpdate->user->chat_id, $stepToUpdate->message_id);
                if (!$nextStep) {
                    CookingDoneEvent::dispatch(
                        $stepToUpdate->user,
                        $stepToUpdate->message_id,
                        $stepToUpdate->recipe,
                    );
                    AskToRateRecipeEvent::dispatch(
                        $stepToUpdate->user,
                        $stepToUpdate->recipe,
                    );
                    continue;
                }
                $bot->sendMessageWithKeyboard(
                    $stepToUpdate->step->step_done_text . "\n" . "\n" . 'Наступний крок: ' . "\n" . $nextStep->description,
                    new InlineKeyboardMarkup($this->buildRecipeStepButtons($nextStep)),
                );
                $stepToUpdate->delete();
                continue;
            }

            $bot->editMessageReplyMarkup(
                $stepToUpdate->user->chat_id,
                $stepToUpdate->message_id,
                new InlineKeyboardMarkup([
                    [[
                        'text' => '⏲️ ' . $stepToUpdate->next_value,
                        'callback_data' => json_encode([]),
                    ]],
                    [[
                        'text' => config('texts')['skip_timer'],
                        'callback_data' => json_encode([
                            'a' => 'skip_timer',
                            'recipe_id' => $stepToUpdate->recipe_id,
                            'step_id' => $stepToUpdate->step->id + 1,
                        ]),
                    ]],
                ])
            );
            $stepToUpdate->update([
                'next_value' => $stepToUpdate->next_value - 1,
            ]);
        }
    }
}
