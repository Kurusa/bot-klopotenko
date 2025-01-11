<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $recipe_id
 * @property string $description
 * @property string $image_url
 * @property int $index
 */
class Step extends Model
{
    protected $table = 'recipe_steps';

    protected $fillable = [
        'recipe_id',
        'description',
        'image_url',
        'index',
    ];

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "<b>" . __('texts.step', ['step' => $this->index]) . "</b>" . "\n \n" . $value,
        )->shouldCache();
    }

    public function hasImage(): bool
    {
        return (bool)$this->image_url;
    }

    public function descriptionExceedLimit(): bool
    {
        return strlen($this->description) >= 1024;
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
