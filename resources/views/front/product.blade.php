@extends('layouts.front')

@section('title', $product->meta_title ?? $product->name . ' - ' . config('app.name', 'Online Store'))
@section('meta_description', $product->meta_description ?? $product->short_description)
@section('meta_keywords', is_array($product->meta_keywords) ? implode(', ', $product->meta_keywords) : '')

@section('content')
<div class="bg-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop') }}" class="hover:text-amber-600">Shop</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $product->name }}</span>
        </nav>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                @else
                <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                    <i class="bi bi-image text-6xl text-gray-400"></i>
                </div>
                @endif
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center gap-2 mb-4">
                    @if($product->isOnSale())
                    <span class="bg-red-600 text-white text-sm font-semibold px-3 py-1 rounded-full">Sale</span>
                    @endif
                    @if($product->product_type === 'digital')
                    <span class="bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                        <i class="bi bi-cloud-download me-1"></i> Instant Download
                    </span>
                    @endif
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                <div class="flex items-center mb-6">
                    @if($product->isOnSale())
                    <span class="text-4xl font-bold text-amber-600">₦{{ number_format($product->sale_price, 2) }}</span>
                    <span class="ml-4 text-2xl text-gray-500 line-through">₦{{ number_format($product->price, 2) }}</span>
                    @else
                    <span class="text-4xl font-bold text-amber-600">₦{{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
                
                <div class="mb-6">
                    @if($product->hasStock())
                    <span class="inline-flex items-center text-green-600 font-medium">
                        <i class="bi bi-check-circle me-2"></i> In Stock
                    </span>
                    @else
                    <span class="inline-flex items-center text-red-600 font-medium">
                        <i class="bi bi-x-circle me-2"></i> Out of Stock
                    </span>
                    @endif
                </div>
                
                @if($product->short_description)
                <p class="text-gray-600 mb-6">{{ $product->short_description }}</p>
                @endif
                
                @if($product->hasStock())
                <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="flex items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock ?? 99 }}" class="w-24 border border-gray-300 rounded-lg px-4 py-3 text-center focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                    </div>
                </form>
                @else
                <button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                    Out of Stock
                </button>
                @endif
                
                @if($product->sku)
                <div class="text-sm text-gray-500 border-t pt-4 mt-4">
                    <span class="font-medium">SKU:</span> {{ $product->sku }}
                </div>
                @endif
            </div>
        </div>
        
        @if($product->description)
        <div class="bg-white rounded-xl shadow-lg p-8 mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Product Description</h2>
            <div class="prose max-w-none text-gray-600">
                {!! $product->description !!}
            </div>
        </div>
        @endif

        @if($product->reviews->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-8 mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
            
            @php
                $avgRating = $product->reviews->avg('rating');
                $ratingCount = $product->reviews->count();
            @endphp
            
            <div class="flex items-center mb-8">
                <div class="text-4xl font-bold text-amber-600 mr-4">{{ number_format($avgRating, 1) }}</div>
                <div>
                    <div class="flex text-amber-500 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                            <i class="bi bi-star-fill"></i>
                            @else
                            <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-gray-500 text-sm">{{ $ratingCount }} review{{ $ratingCount > 1 ? 's' : '' }}</span>
                </div>
            </div>
            
            <div class="space-y-6">
                @foreach($product->reviews->where('is_approved', true) as $review)
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="font-medium text-gray-900 mr-3">{{ $review->name }}</div>
                            <div class="text-amber-500 text-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                    <i class="bi bi-star-fill"></i>
                                    @else
                                    <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <span class="text-gray-500 text-sm">{{ $review->created_at->format('M d, Y') }}</span>
                    </div>
                    <p class="text-gray-600">{{ $review->comment }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($relatedProducts->count() > 0)
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedProducts as $related)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative">
                        @if($related->image)
                        <a href="{{ route('product.show', $related->slug) }}">
                            <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}" class="w-full h-48 object-cover">
                        </a>
                        @else
                        <a href="{{ route('product.show', $related->slug) }}">
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="bi bi-image text-4xl text-gray-400"></i>
                            </div>
                        </a>
                        @endif
                        @if($related->isOnSale())
                        <span class="absolute top-4 right-4 bg-red-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                            Sale
                        </span>
                        @endif
                    </div>
                    <div class="p-6">
                        <a href="{{ route('product.show', $related->slug) }}">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-amber-600 transition">{{ $related->name }}</h3>
                        </a>
                        <div class="flex items-center">
                            @if($related->isOnSale())
                            <span class="text-xl font-bold text-amber-600">₦{{ number_format($related->sale_price, 2) }}</span>
                            <span class="ml-2 text-gray-500 line-through">₦{{ number_format($related->price, 2) }}</span>
                            @else
                            <span class="text-xl font-bold text-amber-600">₦{{ number_format($related->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection