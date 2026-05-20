@extends('layouts.front')

@section('title', 'Payment Successful - ' . config('app.name', 'Online Store'))

@section('content')
<section class="py-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-xl shadow-lg p-12">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-check-circle text-5xl text-green-600"></i>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
            <p class="text-gray-600 mb-8">Thank you for your purchase. Your order has been confirmed.</p>
            
            @if(isset($order) && $order)
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="font-semibold text-gray-900 mb-4">Order Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Product:</span>
                        <span class="font-medium text-gray-900">{{ $order->product_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Quantity:</span>
                        <span class="font-medium text-gray-900">{{ $order->quantity }}</span>
                    </div>
                    @if($order->shipping_cost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium text-gray-900">₦{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping Method:</span>
                        <span class="font-medium text-gray-900">{{ $order->shipping_method ?? 'Standard' }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-900">₦{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="font-semibold text-gray-900">Total Paid:</span>
                        <span class="font-bold text-amber-600">₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="text-gray-900">{{ $order->paid_at->format('F d, Y g:i A') }}</span>
                    </div>
                </div>
            </div>
            
            @if($order->shipping_address)
            <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="font-semibold text-gray-900 mb-4">
                    <i class="bi bi-truck me-2"></i>Shipping Address
                </h3>
                <div class="text-gray-700">
                    <p class="font-medium">{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                    @if(isset($order->shipping_address['address_line2']) && $order->shipping_address['address_line2'])
                        <p>{{ $order->shipping_address['address_line2'] }}</p>
                    @endif
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['country'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                </div>
                @if($order->shipping_method)
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <p class="text-sm text-blue-800">
                        <i class="bi bi-clock me-1"></i>
                        Estimated Delivery: {{ $order->shipping_method }}
                    </p>
                </div>
                @endif
            </div>
            @endif
            
            @if($order->canDownload())
            <div class="mb-8">
                <p class="text-gray-600 mb-4">
                    <i class="bi bi-cloud-download me-2"></i>
                    Your download is ready!
                </p>
                <a href="{{ route('order.download', $order->download_token) }}" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                    <i class="bi bi-download me-2"></i> Download Now
                </a>
                <p class="text-sm text-gray-500 mt-2">
                    @if($order->download_expires_at)
                    Download expires: {{ $order->download_expires_at->format('F d, Y g:i A') }}
                    @else
                    Download available for 24 hours
                    @endif
                </p>
            </div>
            @elseif($order->product && $order->product->product_type === 'digital')
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="text-blue-800 text-sm">
                    <i class="bi bi-info-circle me-2"></i>
                    Your download link has been sent to your email. Please check your inbox to download your purchase.
                </p>
            </div>
            @endif
            
            <div class="border-t pt-6">
                <p class="text-gray-600 mb-4">
                    <i class="bi bi-envelope me-2"></i>
                    We've sent a confirmation email with your order details and download link.
                </p>
                <a href="{{ route('shop') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 font-medium transition">
                    <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                </a>
            </div>
            @else
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <p class="text-gray-600">
                    Your payment has been processed. If you have any questions about your order, please contact support.
                </p>
            </div>
            
            <a href="{{ route('shop') }}" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                <i class="bi bi-bag me-2"></i> Continue Shopping
            </a>
            @endif
        </div>
    </div>
</section>
@endsection