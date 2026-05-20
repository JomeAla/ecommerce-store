<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class AdminShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::with('methods')->orderBy('sort_order')->get();
        return view('admin.settings.shipping_zones', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_codes' => 'nullable|string|max:500',
            'state_codes' => 'nullable|string|max:500',
            'countries' => 'nullable|string|max:500',
            'states' => 'nullable|string|max:500',
            'is_international' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_international'] = $request->has('is_international');
        $validated['is_active'] = true;
        $validated['countries'] = $validated['countries'] ? 
            array_filter(array_map('trim', explode(',', $validated['countries']))) : null;
        $validated['states'] = $validated['states'] ? 
            array_filter(array_map('trim', explode(',', $validated['states']))) : null;

        ShippingZone::create($validated);

        return redirect()->route('admin.shipping.zones')
            ->with('success', 'Shipping zone created successfully!');
    }

    public function update(Request $request, $id)
    {
        $zone = ShippingZone::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_codes' => 'nullable|string|max:500',
            'state_codes' => 'nullable|string|max:500',
            'countries' => 'nullable|string|max:500',
            'states' => 'nullable|string|max:500',
            'is_international' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_international'] = $request->has('is_international');
        $validated['is_active'] = $request->has('is_active');
        $validated['countries'] = $validated['countries'] ? 
            array_filter(array_map('trim', explode(',', $validated['countries']))) : null;
        $validated['states'] = $validated['states'] ? 
            array_filter(array_map('trim', explode(',', $validated['states']))) : null;

        $zone->update($validated);

        return redirect()->route('admin.shipping.zones')
            ->with('success', 'Shipping zone updated successfully!');
    }

    public function destroy($id)
    {
        $zone = ShippingZone::findOrFail($id);
        $zone->delete();

        return redirect()->route('admin.shipping.zones')
            ->with('success', 'Shipping zone deleted successfully!');
    }

    public function toggleActive($id)
    {
        $zone = ShippingZone::findOrFail($id);
        $zone->update(['is_active' => !$zone->is_active]);

        return redirect()->route('admin.shipping.zones')
            ->with('success', 'Shipping zone ' . ($zone->is_active ? 'activated' : 'deactivated') . '!');
    }
}