<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'recipe_id',
        'ingredient_product_id',
        'ingredient_name',
        'quantity',
        'unit',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the recipe that owns the recipe item.
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the ingredient product.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ingredient_product_id');
    }

    /**
     * Calculate total cost for this ingredient.
     */
    public function getTotalCost(): float
    {
        return $this->quantity * $this->unit_cost;
    }
}
