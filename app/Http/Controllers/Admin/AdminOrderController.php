<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $orders = Order::query()
            ->when($status, function ($query) use ($status) {
                $query->where('payment_status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['statusHistory' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->payment_status;
        $newStatus = $request->payment_status;

        $order->update([
            'payment_status' => $newStatus,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $request->notes ?? 'Status updated by admin',
            'created_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Order status updated successfully!');
    }
}