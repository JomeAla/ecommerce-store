@extends('layouts.admin')

@section('title', 'Shipping Zones')
@section('page-title', 'Shipping Zones')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <button onclick="toggleModal('createZoneModal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
            <i class="bi bi-plus-lg me-2"></i>Add Zone
        </button>
    </div>

    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        @if($zones->isEmpty())
            <div class="p-12 text-center">
                <i class="bi bi-globe text-6xl text-slate-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-slate-300 mb-2">No Shipping Zones</h3>
                <p class="text-slate-400 mb-6">Create shipping zones to define delivery areas and rates.</p>
                <button onclick="toggleModal('createZoneModal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="bi bi-plus-lg me-2"></i>Add First Zone
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Zone Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Countries/States</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Methods</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($zones as $zone)
                        <tr class="hover:bg-slate-700/30">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-white">{{ $zone->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-slate-300">
                                    @if($zone->countries)
                                        <span class="text-xs text-slate-400">Countries:</span> {{ implode(', ', (array)$zone->countries) }}
                                    @endif
                                    @if($zone->states)
                                        <br><span class="text-xs text-slate-400">States:</span> {{ implode(', ', (array)$zone->states) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $zone->is_international ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400' }}">
                                    {{ $zone->is_international ? 'International' : 'Domestic' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-300">{{ $zone->methods->count() }} method(s)</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $zone->is_active ? 'bg-green-500/20 text-green-400' : 'bg-slate-600 text-slate-400' }}">
                                    {{ $zone->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button onclick="editZone({{ $zone->id }}, {{ json_encode($zone->name) }}, {{ json_encode($zone->country_codes ?? '') }}, {{ json_encode($zone->state_codes ?? '') }}, {{ json_encode($zone->countries ? implode(',', (array)$zone->countries) : '') }}, {{ json_encode($zone->states ? implode(',', (array)$zone->states) : '') }}, {{ $zone->is_international ? 'true' : 'false' }})" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.shipping.zones.toggle', $zone->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-400 hover:text-yellow-400 hover:bg-yellow-500/10 rounded-lg transition-colors" title="{{ $zone->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi {{ $zone->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.shipping.zones.destroy', $zone->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Delete" onclick="return confirm('Delete this zone?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div id="createZoneModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-slate-800 rounded-lg p-6 w-full max-w-lg mx-4 border border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Create Shipping Zone</h3>
            <button onclick="toggleModal('createZoneModal')" class="text-slate-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.shipping.zones.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Zone Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="e.g., Lagos">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Country Codes (ISO, comma-separated)</label>
                    <input type="text" name="country_codes" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="NG, GH, KE">
                    <p class="mt-1 text-xs text-slate-400">e.g., NG for Nigeria</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">State Names (comma-separated)</label>
                    <input type="text" name="state_codes" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="Lagos, Ogun, Oyo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Country Names (for matching, comma-separated)</label>
                    <input type="text" name="countries" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="Nigeria, Ghana, Kenya">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">State Names (for matching, comma-separated)</label>
                    <input type="text" name="states" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="Lagos, Ikeja, Victoria Island">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_international" name="is_international" value="1" class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_international" class="text-sm text-slate-300">International Zone</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('createZoneModal')" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Create Zone</button>
            </div>
        </form>
    </div>
</div>

<div id="editZoneModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-slate-800 rounded-lg p-6 w-full max-w-lg mx-4 border border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Edit Shipping Zone</h3>
            <button onclick="toggleModal('editZoneModal')" class="text-slate-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="" method="POST" id="editZoneForm">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Zone Name *</label>
                    <input type="text" name="name" id="edit_zone_name" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Country Codes (ISO)</label>
                    <input type="text" name="country_codes" id="edit_zone_country_codes" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">State Codes (comma-separated)</label>
                    <input type="text" name="state_codes" id="edit_zone_state_codes" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Country Names (comma-separated)</label>
                    <input type="text" name="countries" id="edit_zone_countries" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">State Names (comma-separated)</label>
                    <input type="text" name="states" id="edit_zone_states" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="edit_zone_international" name="is_international" value="1" class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_zone_international" class="text-sm text-slate-300">International Zone</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('editZoneModal')" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Update Zone</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function editZone(id, name, countryCodes, stateCodes, countries, states, isInternational) {
    document.getElementById('edit_zone_name').value = name;
    document.getElementById('edit_zone_country_codes').value = countryCodes;
    document.getElementById('edit_zone_state_codes').value = stateCodes;
    document.getElementById('edit_zone_countries').value = countries;
    document.getElementById('edit_zone_states').value = states;
    document.getElementById('edit_zone_international').checked = isInternational;
    document.getElementById('editZoneForm').action = '/admin/shipping/zones/' + id;
    toggleModal('editZoneModal');
}
</script>
@endpush

@endsection