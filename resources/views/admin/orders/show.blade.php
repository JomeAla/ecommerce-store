@extends('layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4">
                <i class="bi bi-arrow-left"></i>
                <span>Back to Orders</span>
            </a>
            <h2 class="text-2xl font-bold text-white">Order {{ $order->order_number }}</h2>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-400' : '' }}
            {{ $order->payment_status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
            {{ $order->payment_status === 'failed' ? 'bg-red-500/20 text-red-400' : '' }}
            {{ $order->payment_status === 'refunded' ? 'bg-blue-500/20 text-blue-400' : '' }}">
            {{ ucfirst($order->payment_status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Customer Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Name</p>
                        <p class="text-base text-white">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Email</p>
                        <p class="text-base text-white">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Phone</p>
                        <p class="text-base text-white">{{ $order->customer_phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Order Date</p>
                        <p class="text-base text-white">{{ $order->created_at->format('F d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            @if($order->shipping_address)
            <div class="bg-slate-800 rounded-lg border border-blue-600 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="bi bi-truck me-2"></i>Shipping Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Shipping Method</p>
                        <p class="text-base text-white">{{ $order->shipping_method ?? 'Standard' }}</p>
                    </div>
                    @if($order->shipping_zone)
                    <div>
                        <p class="text-sm text-slate-400">Shipping Zone</p>
                        <p class="text-base text-white">{{ $order->shipping_zone->name }}</p>
                    </div>
                    @endif
                </div>
                <div class="mt-4 pt-4 border-t border-slate-700">
                    <p class="text-sm text-slate-400">Delivery Address</p>
                    <p class="text-base text-white mt-1">
                        {{ $order->shipping_address['address_line1'] ?? '' }}
                        @if(isset($order->shipping_address['address_line2']) && $order->shipping_address['address_line2'])
                            <br>{{ $order->shipping_address['address_line2'] }}
                        @endif
                        <br>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }}
                        <br>{{ $order->shipping_address['country'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}
                    </p>
                </div>
            </div>
            @endif

            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Product Information</h3>
                <div class="flex items-start gap-4">
                    @if($order->product && $order->product->image)
                        <img src="{{ $order->product->image }}" alt="{{ $order->product_name }}" class="w-20 h-20 object-cover rounded-lg">
                    @else
                        <div class="w-20 h-20 bg-slate-700 rounded-lg flex items-center justify-center">
                            <i class="bi bi-box text-2xl text-slate-500"></i>
                        </div>
                    @endif
                    <div>
                        <p class="text-base font-medium text-white">{{ $order->product_name }}</p>
                        <p class="text-sm text-slate-400">Qty: {{ $order->quantity }}</p>
                        <p class="text-sm text-slate-400">Unit Price: &#8358;{{ number_format($order->unit_price, 2) }}</p>
                        @if($order->product)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-2
                            {{ $order->product->product_type === 'physical' ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                            {{ ucfirst($order->product->product_type) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Payment Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Subtotal</span>
                        <span class="text-white">&#8358;{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->shipping_cost > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Shipping</span>
                        <span class="text-white">&#8358;{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    @endif
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Discount</span>
                        <span class="text-green-400">-&#8358;{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-slate-700">
                        <span class="font-medium text-white">Total</span>
                        <span class="font-bold text-lg text-white">&#8358;{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="pt-3 border-t border-slate-700">
                        <p class="text-sm text-slate-400">Payment Method</p>
                        <p class="text-base text-white">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                        @if($order->payment_reference)
                            <p class="text-sm text-slate-400 mt-1">Reference: {{ $order->payment_reference }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Update Status</h3>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-slate-300 mb-2">Payment Status</label>
                            <select id="payment_status" name="payment_status" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-300 mb-2">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500" placeholder="Add a note about this status change..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-800 rounded-lg border border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Status History</h3>
                @if($order->statusHistory && $order->statusHistory->count() > 0)
                    <div class="space-y-4">
                        @foreach($order->statusHistory as $history)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                @if(!$loop->last)
                                    <div class="w-0.5 h-full bg-slate-600 mt-1"></div>
                                @endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm text-white">{{ ucfirst($history->from_status) }} → {{ ucfirst($history->to_status) }}</p>
                                <p class="text-xs text-slate-400">{{ $history->created_at->format('M d, Y g:i A') }}</p>
                                @if($history->notes)
                                    <p class="text-xs text-slate-400 mt-1">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400">No status history available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection