<?php

namespace App\Traits;

use App\Models\Recipe;

trait RecipeInfoTrait
{
    public function buildHeader(&$message, Recipe $recipe)
    {
        $message .= "<b>" . $recipe->title . "</b>" . "\n" . "\n";
        $message .= '🍽 Порції: ' . $recipe->portions;
        $message .= ' | ⏱ Час: ' . $recipe->time;
        $complexityData = config('constants')['complexity_data'][$recipe->complexity];
        $message .= ' | ⚙ Складність: ' . $complexityData['emoji'] . ' ' . $complexityData['title'];
        $message .= "\n";
    }

    public function buildIngredients(&$message, Recipe $recipe): void
    {
        $message .= "\n" . "<b> Інгредієнти: </b>" . "\n";
        $maxLen = 0;
        foreach ($recipe->ingredients as $ingredient) {
            $len = mb_strlen($ingredient->pivot->quantity . ' ' . $ingredient->unit);
            if ($len > $maxLen) {
                $maxLen = $len;
            }
        }
        foreach ($recipe->ingredients as $ingredient) {
            $lenDiff = $maxLen - mb_strlen($ingredient->pivot->quantity . ' ' . $ingredient->unit);
            $spaces = str_repeat(' ', $lenDiff);
            $message .= '- ' . "<pre>" . $ingredient->pivot->quantity . ' ' . $ingredient->unit . $spaces . "</pre>";
            if ($ingredient->pivot->quantity) $message .= ': ';
            $message .= $ingredient->title . "\n";
        }
    }

    public function buildAdvice(&$message, Recipe $recipe): void
    {
        if ($this->update->getCallbackQuery() && $this->update->getCallbackQueryByKey('a') === 'show_advice') {
            $message .= "\n" . 'ℹ️ Порада: ' . "\n" . $recipe->advice;
        }
    }
}
