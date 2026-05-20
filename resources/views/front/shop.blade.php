@extends('layouts.front')

@section('title', 'Shop - ' . config('app.name', 'Online Store'))

@section('content')
<div class="bg-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Shop</span>
        </nav>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900">Our Products</h1>
            <p class="text-gray-600 mt-2">Browse our collection of premium digital products</p>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="flex items-center space-x-4">
                <form action="{{ route('shop') }}" method="GET" class="flex items-center">
                    @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>
            
            <form action="{{ route('shop') }}" method="GET" class="flex items-center w-full md:w-auto">
                @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative flex-1 md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </form>
        </div>

        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('shop', array_merge(request()->query(), ['category' => null])) }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                All
            </a>
            @php
            $categories = \App\Models\Product::active()->distinct()->pluck('category')->filter();
            @endphp
            @foreach($categories as $category)
            <a href="{{ route('shop', array_merge(request()->query(), ['category' => $category])) }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $category ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                {{ $category }}
            </a>
            @endforeach
        </div>

        @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($products as $product)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="relative">
                    @if($product->image_url)
                    <a href="{{ route('product.show', $product->slug) }}">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    </a>
                    @else
                    <a href="{{ route('product.show', $product->slug) }}">
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="bi bi-image text-4xl text-gray-400"></i>
                        </div>
                    </a>
                    @endif
                    @if($product->isOnSale())
                    <span class="absolute top-4 right-4 bg-red-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                        Sale
                    </span>
                    @endif
                    @if($product->product_type === 'digital')
                    <span class="absolute top-4 left-4 bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                        <i class="bi bi-cloud-download me-1"></i> Digital
                    </span>
                    @endif
                </div>
                <div class="p-6">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-amber-600 transition">{{ $product->name }}</h3>
                    </a>
                    <div class="flex items-center mb-4">
                        @if($product->isOnSale())
                        <span class="text-2xl font-bold text-amber-600">₦{{ number_format($product->sale_price, 2) }}</span>
                        <span class="ml-2 text-gray-500 line-through">₦{{ number_format($product->price, 2) }}</span>
                        @else
                        <span class="text-2xl font-bold text-amber-600">₦{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock ?? 99 }}" class="w-16 border border-gray-300 rounded-lg px-3 py-2 text-center focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white font-medium py-2 px-4 rounded-lg transition flex items-center justify-center">
                            <i class="bi bi-cart-plus me-1"></i> Add
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @if($products->hasPages())
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center gap-2">
                @if($products->onFirstPage())
                <span class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="bi bi-chevron-left"></i>
                </span>
                @else
                <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-amber-600 hover:text-white transition">
                    <i class="bi bi-chevron-left"></i>
                </a>
                @endif
                
                @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if($page == $products->currentPage())
                    <span class="px-4 py-2 bg-amber-600 text-white rounded-lg">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-amber-600 hover:text-white transition">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-amber-600 hover:text-white transition">
                    <i class="bi bi-chevron-right"></i>
                </a>
                @else
                <span class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="bi bi-chevron-right"></i>
                </span>
                @endif
            </nav>
        </div>
        @endif

        @else
        <div class="text-center py-16 bg-white rounded-xl">
            <i class="bi bi-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-500 mb-6">Try adjusting your search or filter criteria</p>
            <a href="{{ route('shop') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-3 rounded-lg transition">
                Clear Filters
            </a>
        </div>
        @endif
    </div>
</section>
@endsection