<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class AdminShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::with('zone')->orderBy('shipping_zone_id')->orderBy('sort_order')->get();
        $zones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.settings.shipping_methods', compact('methods', 'zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'base_cost' => 'required|numeric|min:0',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'delivery_days_min' => 'nullable|integer|min:1',
            'delivery_days_max' => 'nullable|integer|min:1',
            'delivery_time_display' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = true;
        $validated['cost_per_kg'] = $validated['cost_per_kg'] ?? 0;

        ShippingMethod::create($validated);

        return redirect()->route('admin.shipping.methods')
            ->with('success', 'Shipping method created successfully!');
    }

    public function update(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);

        $validated = $request->validate([
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'base_cost' => 'required|numeric|min:0',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'delivery_days_min' => 'nullable|integer|min:1',
            'delivery_days_max' => 'nullable|integer|min:1',
            'delivery_time_display' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['cost_per_kg'] = $validated['cost_per_kg'] ?? 0;
        $validated['free_shipping_threshold'] = $validated['free_shipping_threshold'] ?? null;

        $method->update($validated);

        return redirect()->route('admin.shipping.methods')
            ->with('success', 'Shipping method updated successfully!');
    }

    public function destroy($id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->delete();

        return redirect()->route('admin.shipping.methods')
            ->with('success', 'Shipping method deleted successfully!');
    }

    public function toggleActive($id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->update(['is_active' => !$method->is_active]);

        return redirect()->route('admin.shipping.methods')
            ->with('success', 'Shipping method ' . ($method->is_active ? 'activated' : 'deactivated') . '!');
    }
}