<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingMethod extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'name',
        'description',
        'base_cost',
        'cost_per_kg',
        'free_shipping_threshold',
        'delivery_days_min',
        'delivery_days_max',
        'delivery_time_display',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'base_cost' => 'decimal:2',
        'cost_per_kg' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateCost(float $orderAmount, float $totalWeight = 0): float
    {
        if ($this->free_shipping_threshold && $orderAmount >= (float) $this->free_shipping_threshold) {
            return 0;
        }

        $cost = (float) $this->base_cost;

        if ($totalWeight > 1000) {
            $extraWeight = ($totalWeight - 1000) / 1000;
            $cost += $extraWeight * (float) $this->cost_per_kg;
        }

        return $cost;
    }

    public function getDeliveryEstimateAttribute(): string
    {
        if ($this->delivery_time_display) {
            return $this->delivery_time_display;
        }

        if ($this->delivery_days_min === $this->delivery_days_max) {
            return "{$this->delivery_days_min} business days";
        }

        return "{$this->delivery_days_min}-{$this->delivery_days_max} business days";
    }

    public function isFreeShipping(float $orderAmount): bool
    {
        return $this->free_shipping_threshold !== null && $orderAmount >= (float) $this->free_shipping_threshold;
    }
}