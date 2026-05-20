<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index(Request $request)
    {
        $sessionId = $this->getSessionId();

        $cartItems = Cart::where('session_id', $sessionId)
            ->with('product')
            ->get()
            ->filter(function ($item) {
                return $item->product && $item->product->is_active;
            });

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('info', 'Your cart is empty. Add some products first.');
        }

        $cartSubtotal = $cartItems->sum(function ($item) {
            return (float) ($item->price_at_time * $item->quantity);
        });

        $hasPhysicalProducts = $this->shippingService->hasPhysicalProducts($sessionId);
        $shippingMethods = collect();
        $shippingCost = 0;
        $selectedShippingMethod = null;

        if ($hasPhysicalProducts) {
            $defaultAddress = [
                'country' => 'Nigeria',
                'country_code' => 'NG',
                'state' => '',
                'city' => '',
            ];

            $shippingInfo = $this->shippingService->calculateShipping($defaultAddress, $cartSubtotal, $sessionId);
            $shippingMethods = collect($shippingInfo['methods']);
            
            if ($shippingMethods->isNotEmpty()) {
                $selectedShippingMethod = $shippingMethods->first();
                $shippingCost = $selectedShippingMethod['cost'];
            }
        }

        $cartTotal = $cartSubtotal + $shippingCost;

        $paystackKey = config('services.paystack.public_key');

        $customerData = null;
        if (session()->has('admin_id')) {
            $customerData = [
                'name' => session('admin_name'),
                'email' => 'admin@example.com',
            ];
        }

        return view('front.checkout', compact(
            'cartItems', 
            'cartSubtotal', 
            'cartTotal',
            'shippingCost',
            'shippingMethods',
            'selectedShippingMethod',
            'hasPhysicalProducts',
            'paystackKey', 
            'customerData'
        ));
    }

    public function getShippingRates(Request $request)
    {
        $request->validate([
            'country' => 'required|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $sessionId = $this->getSessionId();

        $cartItems = Cart::where('session_id', $sessionId)
            ->with('product')
            ->get()
            ->filter(function ($item) {
                return $item->product && $item->product->is_active;
            });

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });

        $address = [
            'country' => $request->input('country'),
            'country_code' => $request->input('country_code', ''),
            'state' => $request->input('state', ''),
            'city' => $request->input('city', ''),
        ];

        $shippingInfo = $this->shippingService->calculateShipping($address, $subtotal, $sessionId);

        return response()->json([
            'success' => true,
            'methods' => $shippingInfo['methods'],
            'has_physical' => $shippingInfo['required'],
            'zone' => $shippingInfo['zone'],
        ]);
    }

    public function init(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'shipping_method_id' => 'nullable|integer',
            'shipping_address' => 'nullable|array',
        ]);

        $sessionId = $this->getSessionId();

        $cartItems = Cart::where('session_id', $sessionId)
            ->with('product')
            ->get()
            ->filter(function ($item) {
                return $item->product && $item->product->is_active;
            });

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 422);
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });

        $shippingCost = 0;
        $shippingMethod = null;
        $shippingZoneId = null;
        $shippingMethodName = null;

        $hasPhysical = $this->shippingService->hasPhysicalProducts($sessionId);

        if ($hasPhysical) {
            if ($request->shipping_method_id) {
                $shippingMethod = ShippingMethod::with('zone')->find($request->shipping_method_id);
                if ($shippingMethod) {
                    $shippingCost = $shippingMethod->calculateCost($subtotal, $this->shippingService->getCartTotalWeight($sessionId));
                    $shippingZoneId = $shippingMethod->shipping_zone_id;
                    $shippingMethodName = $shippingMethod->name;
                }
            }
        }

        $total = $subtotal + $shippingCost;

        $customer = Customer::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]
        );

        $downloadToken = md5(uniqid(rand(), true));
        $reference = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        $firstItem = $cartItems->first();

        $orderData = [
            'customer_id' => $customer->id,
            'order_number' => $reference,
            'download_token' => $downloadToken,
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'total_amount' => $total,
            'payment_status' => 'pending',
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'product_id' => $firstItem->product_id,
            'product_name' => $firstItem->product->name,
            'quantity' => $cartItems->sum('quantity'),
            'unit_price' => $firstItem->price_at_time,
            'cart_data' => $cartItems->toArray(),
            'payment_method' => 'paystack',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'shipping_zone_id' => $shippingZoneId,
            'shipping_method' => $shippingMethodName,
            'shipping_cost' => $shippingCost,
        ];

        if ($hasPhysical && isset($validated['shipping_address'])) {
            $orderData['shipping_address'] = $validated['shipping_address'];
        }

        $order = Order::create($orderData);

        return response()->json([
            'success' => true,
            'reference' => $reference,
            'amount' => (int) round($total * 100),
        ]);
    }

    public function success(Request $request)
    {
        $reference = $request->get('reference');

        if (!$reference) {
            return redirect('/checkout')->with('error', 'Invalid payment reference.');
        }

        $order = Order::where('order_number', $reference)->first();

        if (!$order) {
            return redirect('/checkout')->with('error', 'Order not found.');
        }

        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
            
            $this->sendDownloadEmail($order);
        }

        $this->clearCart();

        return view('front.checkout_success', compact('order'));
    }

    public function webhook(Request $request)
    {
        $event = $request->header('x-paystack-event');

        if ($event === 'charge.success') {
            $data = $request->input('data');
            $reference = $data['reference'] ?? null;
            $status = $data['status'] ?? null;

            if ($reference) {
                $order = Order::where('order_number', $reference)->first();

                if ($order && $order->payment_status !== 'paid') {
                    if ($status === 'success') {
                        $order->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        $this->sendDownloadEmail($order);
                    }
                }
            }
        }

        return response()->json(['received' => true]);
    }

    private function clearCart()
    {
        $sessionId = $this->getSessionId();
        Cart::where('session_id', $sessionId)->delete();
    }

    private function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            $uuid = (string) Str::uuid();
            session()->put('cart_session_id', $uuid);
        }

        return session()->get('cart_session_id');
    }

    private function sendDownloadEmail($order)
    {
        try {
            if (view()->exists('emails.order-download')) {
                $downloadUrl = config('app.url') . '/download/' . $order->download_token;

                Mail::send('emails.order-download', [
                    'order' => $order,
                    'downloadUrl' => $downloadUrl,
                ], function ($message) use ($order) {
                    $message->to($order->customer_email, $order->customer_name)
                        ->subject('Your Order #' . $order->order_number . ' - Download Link');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send download email: ' . $e->getMessage());
        }
    }
}