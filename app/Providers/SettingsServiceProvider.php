<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadSettingsFromDatabase();
    }

    protected function loadSettingsFromDatabase(): void
    {
        if (!$this->app->runningInConsole()) {
            try {
                $settings = Cache::rememberForever('settings', function () {
                    return Setting::all()->keyBy('key');
                });

                if (isset($settings['paystack_public_key'])) {
                    $publicKey = $settings['paystack_public_key']->value;
                    if ($publicKey) {
                        Config::set('services.paystack.public_key', $publicKey);
                    }
                }

                if (isset($settings['paystack_secret_key'])) {
                    $secretKey = $settings['paystack_secret_key']->value;
                    if ($secretKey) {
                        Config::set('services.paystack.secret_key', $secretKey);
                    }
                }

                if (isset($settings['store_name'])) {
                    $storeName = $settings['store_name']->value;
                    if ($storeName) {
                        Config::set('app.name', $storeName);
                    }
                }

                if (isset($settings['store_email'])) {
                    $email = $settings['store_email']->value;
                    if ($email) {
                        Config::set('mail.from.address', $email);
                    }
                }

                if (isset($settings['mail_from_name'])) {
                    $name = $settings['mail_from_name']->value;
                    if ($name) {
                        Config::set('mail.from.name', $name);
                    }
                }

            } catch (\Exception $e) {
                // Silently fail if database is not available
            }
        }
    }
}