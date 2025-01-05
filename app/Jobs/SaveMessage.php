<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use App\Services\Keyboard\WeatherKeyboardService;
use App\Services\Weather\WeatherFormatter;
use App\Services\Weather\WeatherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly User  $user,
        private readonly array $update,
    )
    {
    }

    public function handle(): void
    {
        $payload = $this->preparePayload($this->update);

        if ($payload) {
            $this->user->messages()->save(new Message($payload));
        }
    }

    private function preparePayload(array $update): ?array
    {
        if (isset($update['message']['text'])) {
            return ['text' => $update['message']['text']];
        }

        if (isset($update['callback_query']['data'])) {
            return ['text' => $update['callback_query']['data']];
        }

        if (isset($update['message']['location'])) {
            return ['text' => 'sent location'];
        }

        return null;
    }
}
