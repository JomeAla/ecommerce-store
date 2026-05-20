@extends('layouts.admin')

@section('title', 'Add Product')
@section('page-title', 'Add New Product')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Product Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-slate-300 mb-2">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    <p class="mt-1 text-xs text-slate-400">Auto-generated from name if left empty</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-slate-300 mb-2">Category</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}" list="categories" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    <datalist id="categories">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    @error('category')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="product_type" class="block text-sm font-medium text-slate-300 mb-2">Product Type</label>
                    <select id="product_type" name="product_type" onchange="toggleProductTypeFields()" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                        <option value="digital" {{ old('product_type', 'digital') === 'digital' ? 'selected' : '' }}>Digital</option>
                        <option value="physical" {{ old('product_type') === 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="service" {{ old('product_type') === 'service' ? 'selected' : '' }}>Service</option>
                    </select>
                    @error('product_type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="short_description" class="block text-sm font-medium text-slate-300 mb-2">Short Description</label>
                    <input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    @error('short_description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Pricing & Stock</h3>
            <div class="space-y-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-slate-300 mb-2">Price (NGN) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    @error('price')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sale_price" class="block text-sm font-medium text-slate-300 mb-2">Sale Price (NGN)</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    @error('sale_price')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-slate-300 mb-2">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 999) }}" min="0" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="track_stock" name="track_stock" value="1" {{ old('track_stock') ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    <label for="track_stock" class="text-sm text-slate-300">Track stock levels</label>
                </div>
            </div>
        </div>
    </div>

    <div id="physical-fields" class="bg-slate-800 rounded-lg border border-slate-700 p-6 hidden">
        <h3 class="text-lg font-semibold text-white mb-4">Physical Product Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="weight" class="block text-sm font-medium text-slate-300 mb-2">Weight (grams)</label>
                <input type="number" id="weight" name="weight" value="{{ old('weight') }}" step="0.01" min="0" placeholder="e.g., 500" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                @error('weight')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="length" class="block text-sm font-medium text-slate-300 mb-2">Length (cm)</label>
                <input type="number" id="length" name="dimensions[length]" value="{{ old('dimensions.length') }}" step="0.01" min="0" placeholder="e.g., 20" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label for="width" class="block text-sm font-medium text-slate-300 mb-2">Width (cm)</label>
                <input type="number" id="width" name="dimensions[width]" value="{{ old('dimensions.width') }}" step="0.01" min="0" placeholder="e.g., 15" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label for="height" class="block text-sm font-medium text-slate-300 mb-2">Height (cm)</label>
                <input type="number" id="height" name="dimensions[height]" value="{{ old('dimensions.height') }}" step="0.01" min="0" placeholder="e.g., 10" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>
        </div>
    </div>

    <div id="digital-fields" class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Digital Product Details</h3>
        <div class="space-y-4">
            <div>
                <label for="file_path" class="block text-sm font-medium text-slate-300 mb-2">Download File URL</label>
                <input type="url" id="file_path" name="file_path" value="{{ old('file_path') }}" placeholder="https://example.com/file.zip" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                @error('file_path')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="file_name" class="block text-sm font-medium text-slate-300 mb-2">Download File Name</label>
                <input type="text" id="file_name" name="file_name" value="{{ old('file_name') }}" placeholder="e.g., My E-Book.pdf" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                @error('file_name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Product Details</h3>
        <div class="space-y-4">
            <div>
                <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                <textarea id="description" name="description" rows="6" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-slate-300 mb-2">Image URL</label>
                <input type="url" id="image" name="image" value="{{ old('image') }}" placeholder="https://example.com/image.jpg" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 mb-4">
                
                <label for="image_file" class="block text-sm font-medium text-slate-300 mb-2">Or Upload Image</label>
                <input type="file" id="image_file" name="image_file" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                @error('image_file')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                @error('image')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Status</h3>
        <div class="flex flex-wrap gap-6">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-slate-300">Active</label>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-700 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_featured" class="text-sm text-slate-300">Featured</label>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.products.index') }}" class="px-6 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
            Create Product
        </button>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.auto === 'true') {
        slugInput.value = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        slugInput.dataset.auto = 'true';
    }
});

document.getElementById('slug').addEventListener('input', function() {
    this.dataset.auto = 'false';
});

function toggleProductTypeFields() {
    const productType = document.getElementById('product_type').value;
    const physicalFields = document.getElementById('physical-fields');
    const digitalFields = document.getElementById('digital-fields');
    
    if (productType === 'physical') {
        physicalFields.classList.remove('hidden');
        digitalFields.classList.add('hidden');
    } else if (productType === 'digital') {
        physicalFields.classList.add('hidden');
        digitalFields.classList.remove('hidden');
    } else {
        physicalFields.classList.add('hidden');
        digitalFields.classList.add('hidden');
    }
}

toggleProductTypeFields();
</script>
@endpush

@endsection