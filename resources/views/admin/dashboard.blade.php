@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Welcome back, {{ session('admin_name', 'Admin') }}!</h2>
        <p class="text-indigo-200 mt-2">Here's what's happening with your store today.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Total Orders</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['total_orders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                    <i class="bi bi-cart-check text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm text-green-400">
                <i class="bi bi-arrow-up"></i>
                <span>+12% from last month</span>
            </div>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Today's Orders</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['today_orders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-500/10 flex items-center justify-center">
                    <i class="bi bi-calendar-day text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm text-slate-400">
                <i class="bi bi-clock"></i>
                <span>{{ now()->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Revenue</p>
                    <p class="text-2xl font-bold text-white mt-1">${{ number_format($stats['revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center">
                    <i class="bi bi-currency-dollar text-emerald-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm text-green-400">
                <i class="bi bi-arrow-up"></i>
                <span>+8% from last month</span>
            </div>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Pending Orders</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $stats['pending_orders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-500/10 flex items-center justify-center">
                    <i class="bi bi-hourglass-split text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm text-yellow-400">
                <i class="bi bi-exclamation-circle"></i>
                <span>Requires attention</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-900/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Order</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Customer</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Product</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Total</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($recentOrders ?? [] as $order)
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="text-white font-medium">#{{ $order->order_number }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-white">{{ $order->customer->name ?? ($order->customer_name ?? 'Guest') }}</div>
                                    <div class="text-slate-500 text-sm">{{ $order->customer->email ?? ($order->customer_email ?? '') }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-300">
                                    {{ $order->product_name ?? ($order->order->name ?? 'N/A') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="text-white font-medium">₦{{ number_format($order->total_amount, 2) }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                            'paid' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            'failed' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'refunded' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        ];
                                        $statusClass = $statusClasses[$order->payment_status] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-400 text-sm">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-4xl mb-3 block"></i>
                                    No recent orders found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Quick Stats</h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-500/10 flex items-center justify-center">
                            <i class="bi bi-box-seam text-purple-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Total Products</p>
                            <p class="text-lg font-semibold text-white">{{ $stats['total_products'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-500/10 flex items-center justify-center">
                            <i class="bi bi-people text-orange-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Total Customers</p>
                            <p class="text-lg font-semibold text-white">{{ $stats['total_customers'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-cyan-500/10 flex items-center justify-center">
                            <i class="bi bi-star text-cyan-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Featured Products</p>
                            <p class="text-lg font-semibold text-white">{{ $stats['featured_products'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
                            <i class="bi bi-exclamation-triangle text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Low Stock Items</p>
                            <p class="text-lg font-semibold text-white">{{ $stats['low_stock_products'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Top Products</h3>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Product</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Price</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Orders</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Revenue</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($topProducts ?? [] as $product)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-700 flex items-center justify-center">
                                            <i class="bi bi-image text-slate-500"></i>
                                        </div>
                                    @endif
                                    <div>
                                         <p class="text-white font-medium">{{ $product->name }}</p>
                                         <p class="text-slate-500 text-sm">{{ $product->category ?? 'Uncategorized' }}</p>
                                     </div>
                                 </div>
                             </td>
                             <td class="px-5 py-4">
                                 <span class="text-white">₦{{ number_format($product->price, 2) }}</span>
                             </td>
                             <td class="px-5 py-4">
                                 <span class="text-white font-medium">{{ $product->orders_count ?? 0 }}</span>
                             </td>
                             <td class="px-5 py-4">
                                 <span class="text-green-400 font-medium">₦{{ number_format($product->revenue ?? 0, 2) }}</span>
                             </td>
                            <td class="px-5 py-4">
                                @if($product->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">Active</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                <i class="bi bi-box-seam text-4xl mb-3 block"></i>
                                No products found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection