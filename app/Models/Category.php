<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $source_key
 *
 * @property-read Collection<Recipe> $recipes
 */
class Category extends Model
{
    protected $fillable = [
        'title',
        'source_key',
    ];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function titleWithRecipesCount(): string
    {
        return $this->title . ' (' . $this->recipes()->count() . ')';
    }
}
