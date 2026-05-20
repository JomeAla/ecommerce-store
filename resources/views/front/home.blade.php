@extends('layouts.front')

@section('title', 'Home - ' . config('app.name', 'Online Store'))

@section('content')
<section class="hero-gradient text-white py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
            Your Online Store.<br>
            <span class="gradient-text">Launch in 48 Hours.</span>
        </h1>
        <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
            Discover premium digital products and instant downloads. Shop securely with Paystack, Nigeria's trusted payment gateway.
        </p>
        <a href="{{ route('shop') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-8 py-4 rounded-lg transition transform hover:scale-105">
            <i class="bi bi-bag me-2"></i> Shop Now
        </a>
    </div>
</section>

@if($featuredProducts->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Featured Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="relative">
                    @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="bi bi-image text-4xl text-gray-400"></i>
                    </div>
                    @endif
                    @if($product->isOnSale())
                    <span class="absolute top-4 right-4 bg-red-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                        Sale
                    </span>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <div class="flex items-center mb-4">
                        @if($product->isOnSale())
                        <span class="text-2xl font-bold text-amber-600">₦{{ number_format($product->sale_price, 2) }}</span>
                        <span class="ml-2 text-gray-500 line-through">₦{{ number_format($product->price, 2) }}</span>
                        @else
                        <span class="text-2xl font-bold text-amber-600">₦{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-3 rounded-lg transition flex items-center justify-center">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Latest Products</h2>
        @if($latestProducts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestProducts as $product)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                <div class="relative">
                    @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="bi bi-image text-4xl text-gray-400"></i>
                    </div>
                    @endif
                    @if($product->isOnSale())
                    <span class="absolute top-4 right-4 bg-red-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                        Sale
                    </span>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <div class="flex items-center mb-4">
                        @if($product->isOnSale())
                        <span class="text-2xl font-bold text-amber-600">₦{{ number_format($product->sale_price, 2) }}</span>
                        <span class="ml-2 text-gray-500 line-through">₦{{ number_format($product->price, 2) }}</span>
                        @else
                        <span class="text-2xl font-bold text-amber-600">₦{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-3 rounded-lg transition flex items-center justify-center">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <i class="bi bi-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No products available yet.</p>
        </div>
        @endif
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="bg-amber-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-credit-card text-2xl text-amber-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Paystack Integration</h3>
                <p class="text-gray-500 text-sm mt-2">Secure payments in Naira</p>
            </div>
            <div class="text-center">
                <div class="bg-amber-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-geo-alt text-2xl text-amber-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Nigerian Owned</h3>
                <p class="text-gray-500 text-sm mt-2">Proudly local business</p>
            </div>
            <div class="text-center">
                <div class="bg-amber-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-cloud-download text-2xl text-amber-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Instant Download</h3>
                <p class="text-gray-500 text-sm mt-2">Get your files immediately</p>
            </div>
            <div class="text-center">
                <div class="bg-amber-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-headset text-2xl text-amber-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">24/7 Support</h3>
                <p class="text-gray-500 text-sm mt-2">We're here to help</p>
            </div>
        </div>
    </div>
</section>

<section class="hero-gradient py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to start selling?</h2>
        <p class="text-gray-300 mb-8 max-w-2xl mx-auto">
            Browse our collection of premium products and find exactly what you need.
        </p>
        <a href="{{ route('shop') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold px-8 py-4 rounded-lg transition">
            <i class="bi bi-arrow-right me-2"></i> Browse Shop
        </a>
    </div>
</section>
@endsection