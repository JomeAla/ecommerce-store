@extends('layouts.front')

@section('title', 'Shopping Cart - ' . config('app.name', 'Online Store'))

@section('content')
<div class="bg-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Cart</span>
        </nav>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Shopping Cart</h1>
        
        @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="hidden md:grid grid-cols-12 gap-4 p-4 bg-gray-50 font-medium text-gray-700 border-b">
                        <div class="col-span-3">Product</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-3 text-center">Quantity</div>
                        <div class="col-span-2 text-center">Subtotal</div>
                        <div class="col-span-2 text-center">Action</div>
                    </div>
                    
                    @foreach($cartItems as $item)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border-b items-center">
                        <div class="col-span-3 flex items-center">
                            @if($item->product && $item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg mr-4">
                            @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <i class="bi bi-image text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('product.show', $item->product->slug ?? '#') }}" class="font-medium text-gray-900 hover:text-amber-600">
                                    {{ $item->product->name ?? 'Product' }}
                                </a>
                                @if($item->product && $item->product->product_type === 'digital')
                                <div class="text-xs text-blue-600 mt-1">
                                    <i class="bi bi-cloud-download me-1"></i> Digital
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-span-2 text-center md:block">
                            <span class="md:hidden font-medium text-gray-500">Price: </span>
                            <span class="text-gray-900">₦{{ number_format($item->price_at_time, 2) }}</span>
                        </div>
                        
                        <div class="col-span-3 flex justify-center items-center md:block">
                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="w-16 border border-gray-300 rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <button type="submit" class="ml-2 text-amber-600 hover:text-amber-700" title="Update quantity">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </form>
                        </div>
                        
                        <div class="col-span-2 text-center md:block">
                            <span class="md:hidden font-medium text-gray-500">Subtotal: </span>
                            <span class="font-semibold text-gray-900">₦{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                        
                        <div class="col-span-2 text-center md:block">
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                <button type="submit" class="text-red-600 hover:text-red-700" title="Remove item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Cart Summary</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                            <span class="font-medium">₦{{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-medium">Free</span>
                        </div>
                        <div class="border-t pt-4 flex justify-between text-xl font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-amber-600">₦{{ number_format($cartTotal, 2) }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('checkout') }}" class="block w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                        <i class="bi bi-lock me-2"></i> Proceed to Checkout
                    </a>
                    
                    <a href="{{ route('shop') }}" class="block w-full mt-3 text-center text-gray-600 hover:text-amber-600 font-medium py-2 transition">
                        <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        
        @else
        <div class="text-center py-16 bg-white rounded-xl shadow-lg">
            <i class="bi bi-cart-x text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
            <p class="text-gray-500 mb-6">Looks like you haven't added any products to your cart yet.</p>
            <a href="{{ route('shop') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-3 rounded-lg transition">
                <i class="bi bi-bag me-2"></i> Start Shopping
            </a>
        </div>
        @endif
    </div>
</section>
@endsection