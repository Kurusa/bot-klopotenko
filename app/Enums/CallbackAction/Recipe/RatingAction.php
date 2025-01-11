<?php

namespace App\Enums\CallbackAction\Recipe;

use App\Enums\CallbackAction\CallbackActionEnum;
use App\Http\Controllers\Recipe\Rate\HandleRateRecipe;

enum RatingAction: int implements CallbackActionEnum
{
    case VERY_BAD = 16;
    case BAD = 17;
    case MIDDLE = 18;
    case GOOD = 19;
    case VERY_GOOD = 20;

    public function handler(): string
    {
        return match ($this) {
            self::VERY_BAD, self::BAD, self::MIDDLE, self::GOOD, self::VERY_GOOD => HandleRateRecipe::class,
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::VERY_BAD => '️☹️',
            self::BAD => '😕',
            self::MIDDLE => '😐',
            self::GOOD => '🙂',
            self::VERY_GOOD => '😊',
        };
    }

    public function getValue(): int
    {
        return match ($this) {
            self::VERY_BAD => 1,
            self::BAD => 2,
            self::MIDDLE => 3,
            self::GOOD => 4,
            self::VERY_GOOD => 5,
        };
    }
}
