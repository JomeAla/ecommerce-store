@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order number, name, email..." class="px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            <select name="status" class="px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:border-indigo-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center">
                <i class="bi bi-receipt text-6xl text-slate-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-slate-300 mb-2">No Orders Found</h3>
                <p class="text-slate-400">Orders will appear here when customers make purchases.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Order #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-white">{{ $order->order_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-white">{{ $order->customer_name }}</div>
                                <div class="text-xs text-slate-400">{{ $order->customer_email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-300">{{ $order->product_name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-white">&#8358;{{ number_format($order->total_amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $order->payment_status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $order->payment_status === 'failed' ? 'bg-red-500/20 text-red-400' : '' }}
                                    {{ $order->payment_status === 'refunded' ? 'bg-blue-500/20 text-blue-400' : '' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-slate-400">{{ $order->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 rounded-lg transition-colors">
                                    <i class="bi bi-eye"></i>
                                    <span>View</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($orders->hasPages())
    <div class="flex justify-center">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection