<?php

namespace App\Traits;

use App\Models\Recipe;

trait RecipeInfoTrait
{
    public function buildAdvice(&$message, Recipe $recipe): void
    {
        if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') === 'show_advice') {
            $message .= "\n" . 'ℹ️ Порада: ' . "\n" . $recipe->advice;
        }
    }
}
