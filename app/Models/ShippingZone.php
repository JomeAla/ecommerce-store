<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'countries',
        'states',
        'country_codes',
        'state_codes',
        'is_international',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'countries' => 'array',
        'states' => 'array',
        'is_international' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function methods(): HasMany
    {
        return $this->hasMany(ShippingMethod::class, 'shipping_zone_id')->where('is_active', true)->orderBy('sort_order');
    }

    public function allMethods(): HasMany
    {
        return $this->hasMany(ShippingMethod::class, 'shipping_zone_id')->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInternational($query)
    {
        return $query->where('is_international', true);
    }

    public function scopeDomestic($query)
    {
        return $query->where('is_international', false);
    }

    public function matchesAddress(array $address): bool
    {
        $country = strtolower($address['country'] ?? '');
        $countryCode = strtolower($address['country_code'] ?? '');
        $state = strtolower($address['state'] ?? '');

        $countryCodes = array_filter(array_map('trim', explode(',', strtolower($this->country_codes ?? ''))));
        $stateCodes = array_filter(array_map('trim', explode(',', strtolower($this->state_codes ?? ''))));
        $countries = array_filter(array_map('trim', array_map('strtolower', (array) $this->countries)));
        $states = array_filter(array_map('trim', array_map('strtolower', (array) $this->states)));

        if (!empty($countryCodes) && (in_array($countryCode, $countryCodes) || in_array($country, $countryCodes))) {
            if (!empty($stateCodes)) {
                return in_array($state, $stateCodes);
            }
            return true;
        }

        if (!empty($countries) && (in_array($country, $countries) || in_array($countryCode, $countries))) {
            if (!empty($states)) {
                return in_array($state, $states);
            }
            return true;
        }

        return false;
    }

    public static function findByAddress(array $address): ?self
    {
        return static::active()->orderBy('sort_order')->get()->first(function ($zone) use ($address) {
            return $zone->matchesAddress($address);
        });
    }
}