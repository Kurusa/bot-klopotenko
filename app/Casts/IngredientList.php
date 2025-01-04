<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class IngredientList implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): string
    {
        $message = "\n" . __('texts.ingredients') . "\n";
        $maxLen = $model->ingredients_collection->max(fn($ingredient) => mb_strlen($ingredient['quantity'] . ' ' . $ingredient['unit']));

        $message .= '<pre>';
        foreach ($model->ingredients_collection as $ingredient) {
            $lenDiff = $maxLen - mb_strlen($ingredient['quantity'] . ' ' . $ingredient['unit']);
            $spaces = str_repeat(' ', $lenDiff);
            $message .= '- ' . $ingredient['quantity'] . ' ' . $ingredient['unit'] . $spaces;
            if ($ingredient['quantity']) {
                $message .= ': ';
            }
            $message .= $ingredient['title'] . "\n";
        }
        $message .= '</pre>';

        return $message;
    }

    public function set($model, string $key, $value, array $attributes)
    {
    }
}
