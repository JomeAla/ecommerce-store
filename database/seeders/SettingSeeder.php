<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'store_name',
                'value' => 'E-Shop Nigeria',
            ],
            [
                'key' => 'store_email',
                'value' => 'hello@eshop.com',
            ],
            [
                'key' => 'currency',
                'value' => 'NGN',
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@eshop.com',
            ],
            [
                'key' => 'paystack_test_mode',
                'value' => 'true',
            ],
            [
                'key' => 'default_order_status',
                'value' => 'pending',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}