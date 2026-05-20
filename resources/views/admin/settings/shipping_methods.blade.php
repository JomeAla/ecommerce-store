@extends('layouts.admin')

@section('title', 'Shipping Methods')
@section('page-title', 'Shipping Methods')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.shipping.zones') }}" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">
            <i class="bi bi-arrow-left me-2"></i>Manage Zones
        </a>
        <button onclick="toggleModal('createMethodModal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors ml-2">
            <i class="bi bi-plus-lg me-2"></i>Add Method
        </button>
    </div>

    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        @if($methods->isEmpty())
            <div class="p-12 text-center">
                <i class="bi bi-truck text-6xl text-slate-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-slate-300 mb-2">No Shipping Methods</h3>
                <p class="text-slate-400 mb-6">Create zones first, then add shipping methods for each zone.</p>
                <a href="{{ route('admin.shipping.zones') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="bi bi-plus-lg me-2"></i>Manage Zones
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Method Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Zone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Base Cost</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Cost/kg</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Free Shipping</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Delivery</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($methods as $method)
                        <tr class="hover:bg-slate-700/30">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-white">{{ $method->name }}</div>
                                <div class="text-xs text-slate-400">{{ $method->description ?? 'No description' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-300">{{ $method->zone->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-white">₦{{ number_format($method->base_cost, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-300">₦{{ number_format($method->cost_per_kg, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($method->free_shipping_threshold)
                                    <span class="text-sm text-green-400">Orders > ₦{{ number_format($method->free_shipping_threshold, 2) }}</span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-300">{{ $method->delivery_estimate }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $method->is_active ? 'bg-green-500/20 text-green-400' : 'bg-slate-600 text-slate-400' }}">
                                    {{ $method->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button onclick="editMethod({{ $method->id }}, '{{ $method->shipping_zone_id }}', '{{ $method->name }}', '{{ $method->description ?? '' }}', {{ $method->base_cost }}, {{ $method->cost_per_kg }}, '{{ $method->free_shipping_threshold ?? '' }}', {{ $method->delivery_days_min }}, {{ $method->delivery_days_max }}, '{{ $method->delivery_time_display ?? '' }}')" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.shipping.methods.toggle', $method->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-400 hover:text-yellow-400 hover:bg-yellow-500/10 rounded-lg transition-colors" title="{{ $method->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi {{ $method->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.shipping.methods.destroy', $method->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Delete" onclick="return confirm('Delete this method?')">
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

<div id="createMethodModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-slate-800 rounded-lg p-6 w-full max-w-lg mx-4 border border-slate-700 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Create Shipping Method</h3>
            <button onclick="toggleModal('createMethodModal')" class="text-slate-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.shipping.methods.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Zone *</label>
                    <select name="shipping_zone_id" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Select Zone</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $zone->is_international ? 'International' : 'Domestic' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Method Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="e.g., Standard Delivery">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                    <input type="text" name="description" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="e.g., Doorstep delivery">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Base Cost (₦) *</label>
                        <input type="number" name="base_cost" step="0.01" min="0" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Cost per kg (₦)</label>
                        <input type="number" name="cost_per_kg" step="0.01" min="0" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Free Shipping Threshold (₦)</label>
                    <input type="number" name="free_shipping_threshold" step="0.01" min="0" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="Leave empty to disable">
                    <p class="mt-1 text-xs text-slate-400">Shipping is free when order amount exceeds this value</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Min Delivery Days</label>
                        <input type="number" name="delivery_days_min" min="1" value="1" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Max Delivery Days</label>
                        <input type="number" name="delivery_days_max" min="1" value="5" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Delivery Time Display</label>
                    <input type="text" name="delivery_time_display" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="e.g., 2-5 business days">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('createMethodModal')" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Create Method</button>
            </div>
        </form>
    </div>
</div>

<div id="editMethodModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-slate-800 rounded-lg p-6 w-full max-w-lg mx-4 border border-slate-700 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Edit Shipping Method</h3>
            <button onclick="toggleModal('editMethodModal')" class="text-slate-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="" method="POST" id="editMethodForm">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Zone *</label>
                    <select name="shipping_zone_id" id="edit_method_zone" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Method Name *</label>
                    <input type="text" name="name" id="edit_method_name" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                    <input type="text" name="description" id="edit_method_description" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Base Cost (₦) *</label>
                        <input type="number" name="base_cost" id="edit_method_base_cost" step="0.01" min="0" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Cost per kg (₦)</label>
                        <input type="number" name="cost_per_kg" id="edit_method_cost_per_kg" step="0.01" min="0" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Free Shipping Threshold (₦)</label>
                    <input type="number" name="free_shipping_threshold" id="edit_method_free_threshold" step="0.01" min="0" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Min Delivery Days</label>
                        <input type="number" name="delivery_days_min" id="edit_method_delivery_min" min="1" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Max Delivery Days</label>
                        <input type="number" name="delivery_days_max" id="edit_method_delivery_max" min="1" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Delivery Time Display</label>
                    <input type="text" name="delivery_time_display" id="edit_method_delivery_display" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('editMethodModal')" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Update Method</button>
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

function editMethod(id, zoneId, name, description, baseCost, costPerKg, freeThreshold, deliveryMin, deliveryMax, deliveryDisplay) {
    document.getElementById('edit_method_zone').value = zoneId;
    document.getElementById('edit_method_name').value = name;
    document.getElementById('edit_method_description').value = description;
    document.getElementById('edit_method_base_cost').value = baseCost;
    document.getElementById('edit_method_cost_per_kg').value = costPerKg;
    document.getElementById('edit_method_free_threshold').value = freeThreshold;
    document.getElementById('edit_method_delivery_min').value = deliveryMin;
    document.getElementById('edit_method_delivery_max').value = deliveryMax;
    document.getElementById('edit_method_delivery_display').value = deliveryDisplay;
    document.getElementById('editMethodForm').action = '/admin/shipping/methods/' + id;
    toggleModal('editMethodModal');
}
</script>
@endpush

@endsection