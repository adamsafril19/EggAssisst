<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'current_stock_kg',
        'initial_stock_kg',
        'current_price',
        'alert_threshold_days',
        'lead_time_days',
        'avg_egg_weight',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_stock_kg' => 'decimal:2',
        'initial_stock_kg' => 'decimal:2',
        'current_price' => 'decimal:2',
        'alert_threshold_days' => 'decimal:2',
        'lead_time_days' => 'integer',
        'avg_egg_weight' => 'decimal:4',
    ];

    /**
     * Get the active product. Single query, no hardcoded ID.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Relationships
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->orderBy('created_at', 'desc');
    }
}
