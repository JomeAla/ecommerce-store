<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $groupedSettings = Setting::all()->groupBy('group');
        
        $settings = [
            'general' => $groupedSettings->get('general', new Collection),
            'payment' => $groupedSettings->get('payment', new Collection),
            'email' => $groupedSettings->get('email', new Collection),
        ];
        
        $zones = ShippingZone::with('methods')->orderBy('sort_order')->get();

        return view('admin.settings.index', compact('settings', 'zones'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $settings = $request->input('settings');

        foreach ($settings as $key => $value) {
            $group = $this->determineGroup($key);
            $type = $this->determineType($key);

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'type' => $type,
                    'is_public' => false,
                ]
            );
        }

        Cache::forget('settings');

        return redirect()->route('admin.settings')
            ->with('success', 'Settings saved successfully!');
    }

    private function determineGroup(string $key): string
    {
        if (str_starts_with($key, 'paystack')) {
            return 'payment';
        }
        if (str_starts_with($key, 'mail')) {
            return 'email';
        }
        return 'general';
    }

    private function determineType(string $key): string
    {
        if (str_contains($key, 'address')) {
            return 'textarea';
        }
        if (str_contains($key, 'public_key') || str_contains($key, 'secret_key') || str_contains($key, 'password')) {
            return 'text';
        }
        return 'text';
    }
}