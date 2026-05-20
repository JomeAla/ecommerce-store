<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use Illuminate\Support\Collection;

class ShippingService
{
    public function getCartTotalWeight(string $sessionId): float
    {
        $cartItems = Cart::where('session_id', $sessionId)
            ->with('product')
            ->get()
            ->filter(fn($item) => $item->product && $item->product->product_type === 'physical');

        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $weight = $item->product->weight ?? 0;
            $totalWeight += $weight * $item->quantity;
        }

        return $totalWeight;
    }

    public function hasPhysicalProducts(string $sessionId): bool
    {
        return Cart::where('session_id', $sessionId)
            ->with('product')
            ->get()
            ->filter(fn($item) => $item->product && $item->product->product_type === 'physical')
            ->isNotEmpty();
    }

    public function detectZone(array $address): ?ShippingZone
    {
        return ShippingZone::findByAddress($address);
    }

    public function getAvailableMethods(?ShippingZone $zone, float $orderAmount, float $totalWeight = 0): Collection
    {
        if (!$zone) {
            $zone = ShippingZone::active()->orderBy('sort_order')->first();
        }

        if (!$zone) {
            return collect();
        }

        $methods = $zone->methods;

        return $methods->map(function ($method) use ($orderAmount, $totalWeight) {
            $cost = $method->calculateCost($orderAmount, $totalWeight);
            $isFree = $method->isFreeShipping($orderAmount);

            return [
                'id' => $method->id,
                'name' => $method->name,
                'description' => $method->description,
                'cost' => $cost,
                'is_free' => $isFree,
                'delivery_estimate' => $method->delivery_estimate,
                'formatted_cost' => $isFree ? 'Free' : '₦' . number_format($cost, 2),
            ];
        });
    }

    public function calculateShipping(array $address, float $orderAmount, string $sessionId): array
    {
        $hasPhysical = $this->hasPhysicalProducts($sessionId);

        if (!$hasPhysical) {
            return [
                'required' => false,
                'zone' => null,
                'methods' => [],
                'default_cost' => 0,
            ];
        }

        $zone = $this->detectZone($address);
        $totalWeight = $this->getCartTotalWeight($sessionId);
        $methods = $this->getAvailableMethods($zone, $orderAmount, $totalWeight);

        $defaultCost = 0;
        if ($methods->isNotEmpty()) {
            $defaultCost = $methods->first()['cost'];
        }

        return [
            'required' => true,
            'zone' => $zone ? [
                'id' => $zone->id,
                'name' => $zone->name,
            ] : null,
            'methods' => $methods->toArray(),
            'default_cost' => $defaultCost,
            'total_weight' => $totalWeight,
        ];
    }

    public function getZoneByAddress(array $address): ?ShippingZone
    {
        return $this->detectZone($address);
    }

    public static function formatWeight(float $grams): string
    {
        if ($grams >= 1000) {
            return number_format($grams / 1000, 2) . ' kg';
        }
        return number_format($grams, 0) . ' g';
    }
}