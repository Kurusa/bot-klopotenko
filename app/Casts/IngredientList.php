<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class IngredientList implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        $message = "\n" . "<b> Інгредієнти: </b>" . "\n";
        $maxLen = 0;
        foreach ($model->ingredients as $ingredient) {
            $len = mb_strlen($ingredient->pivot->quantity . ' ' . $ingredient->unit);
            if ($len > $maxLen) {
                $maxLen = $len;
            }
        }
        foreach ($model->ingredients as $ingredient) {
            $lenDiff = $maxLen - mb_strlen($ingredient->pivot->quantity . ' ' . $ingredient->unit);
            $spaces = str_repeat(' ', $lenDiff);
            $message .= '- ' . "<pre>" . $ingredient->pivot->quantity . ' ' . $ingredient->unit . $spaces . "</pre>";
            if ($ingredient->pivot->quantity) $message .= ': ';
            $message .= $ingredient->title . "\n";
        }

        return $message;
    }

    public function set($model, string $key, $value, array $attributes)
    {
    }
}
