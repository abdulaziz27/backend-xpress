<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\StoreScope;

class Recipe extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id',
        'product_id',
        'name',
        'description',
        'yield_quantity',
        'yield_unit',
        'preparation_time',
        'cooking_time',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'yield_quantity' => 'decimal:2',
        'preparation_time' => 'integer',
        'cooking_time' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new StoreScope);
    }

    /**
     * Get the store that owns the recipe.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the product associated with the recipe.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the recipe items (ingredients).
     */
    public function items(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    /**
     * Calculate total cost of recipe.
     */
    public function calculateTotalCost(): float
    {
        return $this->items()->sum(function ($item) {
            return $item->quantity * $item->unit_cost;
        });
    }

    /**
     * Calculate cost per unit.
     */
    public function calculateUnitCost(): float
    {
        $totalCost = $this->calculateTotalCost();
        return $this->yield_quantity > 0 ? $totalCost / $this->yield_quantity : 0;
    }

    /**
     * Scope to get active recipes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
