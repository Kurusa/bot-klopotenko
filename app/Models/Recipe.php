<?php

namespace App\Models;

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
    ];

    protected function advice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => trim($value),
        );
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
