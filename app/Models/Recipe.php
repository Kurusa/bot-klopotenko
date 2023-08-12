<?php

namespace App\Models;

use App\Casts\IngredientList;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'title',
        'complexity',
        'advice',
        'time',
        'portions',
        'source_url',
        'category_id',
        'status',
        'source_url',
        'image_url',
        'is_popular',
    ];

    protected $casts = [
        'ingredient_list' => IngredientList::class,
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ($this->is_popular ? '⭐ ' : '') . $value . ' ' . $this->complexity_emoji,
        )->shouldCache();
    }

    protected function complexityTitle(): Attribute
    {
        return Attribute::make(
            get: fn($value) => config('constants')['complexity_data'][$this->complexity]['title'],
        )->shouldCache();
    }

    protected function complexityEmoji(): Attribute
    {
        return Attribute::make(
            get: fn($value) => config('constants')['complexity_data'][$this->complexity]['emoji'],
        )->shouldCache();
    }

    protected function advice(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "\n" . 'ℹ️ Порада: ' . "\n" . trim($value),
        )->shouldCache();
    }

    protected function header(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "<b>" . $this->title . "</b>" . "\n" . "\n" .
                '🍽 Порції: ' . $this->portions .
                ' | ⏱ Час: ' . $this->time .
                ' | ⚙ Складність: ' . $this->complexity_emoji . ' ' . $this->complexity_title .
                "\n"
        )->shouldCache();
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')->withPivot('quantity');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }
}
