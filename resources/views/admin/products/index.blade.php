@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                <i class="bi bi-plus-lg"></i>
                <span>Add Product</span>
            </a>
        </div>
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            <select name="category" class="px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:border-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        @if($products->isEmpty())
            <div class="p-12 text-center">
                <i class="bi bi-box-seam text-6xl text-slate-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-slate-300 mb-2">No Products Found</h3>
                <p class="text-slate-400 mb-6">Get started by adding your first product.</p>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add Product</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($products as $product)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                @else
                                    <div class="w-12 h-12 bg-slate-700 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-image text-slate-500"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-white">{{ $product->name }}</div>
                                <div class="text-xs text-slate-400">{{ $product->slug }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-white">&#8358;{{ number_format($product->price, 2) }}</div>
                                @if($product->sale_price)
                                    <div class="text-xs text-green-400">&#8358;{{ number_format($product->sale_price, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-300">{{ $product->stock ?? '∞' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-600 text-slate-300">
                                    {{ $product->category ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $product->is_active ? '0' : '1' }}">
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-500/20 text-green-400' : 'bg-slate-600 text-slate-400' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-colors" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Delete" onclick="return confirm('Are you sure you want to delete this product?')">
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

    @if($products->hasPages())
    <div class="flex justify-center">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection