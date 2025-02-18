<?php

namespace App\Services;

use App\Enums\CallbackAction\BackAction;
use App\Enums\CallbackAction\CallbackAction;
use App\Enums\CallbackAction\Recipe\AdviceAction;
use App\Enums\CallbackAction\Recipe\RatingAction;
use App\Enums\CallbackAction\Recipe\SavedAction;
use App\Enums\CallbackAction\Recipe\StepAction;

class CallbackActionRegistry
{
    public static function getEnums(): array
    {
        return [
            CallbackAction::class,
            BackAction::class,
            AdviceAction::class,
            SavedAction::class,
            StepAction::class,
            RatingAction::class,
        ];
    }
}
