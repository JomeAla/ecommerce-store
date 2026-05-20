<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        $lagos = ShippingZone::create([
            'name' => 'Lagos',
            'country_codes' => 'ng',
            'state_codes' => 'lagos',
            'countries' => ['Nigeria'],
            'states' => ['Lagos', 'Ikeja', 'Victoria Island', 'Lekki', 'Epe', 'Badagry'],
            'is_international' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $lagos->id,
            'name' => 'Lagos Standard',
            'description' => 'Door-to-door delivery within Lagos',
            'base_cost' => 2500,
            'cost_per_kg' => 500,
            'free_shipping_threshold' => 50000,
            'delivery_days_min' => 1,
            'delivery_days_max' => 3,
            'delivery_time_display' => '1-3 business days',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $lagos->id,
            'name' => 'Lagos Express',
            'description' => 'Same-day/next-day delivery within Lagos',
            'base_cost' => 5000,
            'cost_per_kg' => 1000,
            'free_shipping_threshold' => 100000,
            'delivery_days_min' => 1,
            'delivery_days_max' => 1,
            'delivery_time_display' => 'Same/Next business day',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $abuja = ShippingZone::create([
            'name' => 'Abuja & North Central',
            'country_codes' => 'ng',
            'state_codes' => 'abuja,fct,niger,kogi,nasarawa,plateau,benue',
            'countries' => ['Nigeria'],
            'states' => ['Abuja', 'Niger', 'Kogi', 'Nasarawa', 'Plateau', 'Benue'],
            'is_international' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $abuja->id,
            'name' => 'Abuja Standard',
            'description' => 'Standard delivery to Abuja and North Central',
            'base_cost' => 3500,
            'cost_per_kg' => 500,
            'free_shipping_threshold' => 50000,
            'delivery_days_min' => 2,
            'delivery_days_max' => 5,
            'delivery_time_display' => '2-5 business days',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $portHarcourt = ShippingZone::create([
            'name' => 'Port Harcourt & South South',
            'country_codes' => 'ng',
            'state_codes' => 'rivers,delta,edo,akwa ibom,cross river,bayelsa,abenorth',
            'countries' => ['Nigeria'],
            'states' => ['Rivers', 'Delta', 'Edo', 'Akwa Ibom', 'Cross River', 'Bayelsa'],
            'is_international' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $portHarcourt->id,
            'name' => 'PH Standard',
            'description' => 'Standard delivery to Port Harcourt and South South',
            'base_cost' => 4000,
            'cost_per_kg' => 500,
            'free_shipping_threshold' => 50000,
            'delivery_days_min' => 2,
            'delivery_days_max' => 5,
            'delivery_time_display' => '2-5 business days',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $otherNigeria = ShippingZone::create([
            'name' => 'Other Nigerian States',
            'country_codes' => 'ng',
            'countries' => ['Nigeria'],
            'is_international' => false,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $otherNigeria->id,
            'name' => 'National Standard',
            'description' => 'Standard delivery to other Nigerian states',
            'base_cost' => 4500,
            'cost_per_kg' => 500,
            'free_shipping_threshold' => 50000,
            'delivery_days_min' => 3,
            'delivery_days_max' => 7,
            'delivery_time_display' => '3-7 business days',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $westAfrica = ShippingZone::create([
            'name' => 'West Africa',
            'country_codes' => 'gh,ghana,ke,kenya,ci,cote d\'ivoire,tg,togo',
            'countries' => ['Ghana', 'Kenya', "Cote d'Ivoire", 'Togo'],
            'is_international' => true,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $westAfrica->id,
            'name' => 'West Africa Delivery',
            'description' => 'Standard delivery to West African countries',
            'base_cost' => 15000,
            'cost_per_kg' => 2000,
            'free_shipping_threshold' => 100000,
            'delivery_days_min' => 5,
            'delivery_days_max' => 10,
            'delivery_time_display' => '5-10 business days',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $international = ShippingZone::create([
            'name' => 'International',
            'country_codes' => 'us,gb,ca,uk,au,de,fr',
            'countries' => ['United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France'],
            'is_international' => true,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $international->id,
            'name' => 'International Standard',
            'description' => 'Standard international shipping',
            'base_cost' => 25000,
            'cost_per_kg' => 5000,
            'free_shipping_threshold' => 200000,
            'delivery_days_min' => 7,
            'delivery_days_max' => 14,
            'delivery_time_display' => '7-14 business days',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ShippingMethod::create([
            'shipping_zone_id' => $international->id,
            'name' => 'International Express',
            'description' => 'Express international shipping',
            'base_cost' => 45000,
            'cost_per_kg' => 8000,
            'free_shipping_threshold' => 500000,
            'delivery_days_min' => 3,
            'delivery_days_max' => 7,
            'delivery_time_display' => '3-7 business days',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}