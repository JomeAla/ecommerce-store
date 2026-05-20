<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Frontend Cart Controller
 *
 * Handles shopping cart operations including add, remove, update items
 *
 * @author E-commerce Starter Kit
 * @version 1.0.0
 */
class CartController extends Controller
{
    /**
     * Display the shopping cart page
     *
     * @method GET
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sessionId = $this->getSessionId();

        $cartItems = Cart::where('session_id', $sessionId)
            ->with('product')
            ->get()
            ->filter(function ($item) {
                return $item->product && $item->product->is_active;
            });

        $cartTotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('front.cart', compact('cartItems', 'cartTotal'));
    }

    /**
     * Add a product to the shopping cart
     *
     * @method POST
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->active()
            ->first();

        if (!$product) {
            session()->flash('error', 'Product not found or unavailable.');
            return redirect()->back();
        }

        if ($product->stock < $validated['quantity']) {
            session()->flash('error', 'Requested quantity exceeds available stock.');
            return redirect()->back();
        }

        $sessionId = $this->getSessionId();

        $cartItem = Cart::where('session_id', $sessionId)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            if ($product->stock < $newQuantity) {
                session()->flash('error', 'Total quantity exceeds available stock.');
                return redirect()->back();
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'price_at_time' => $product->price,
            ]);
        }

        session()->flash('success', 'Product added to cart!');

        return redirect('/cart');
    }

    /**
     * Remove a product from the shopping cart
     *
     * @method POST
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|integer|exists:carts,id',
        ]);

        $sessionId = $this->getSessionId();

        Cart::where('session_id', $sessionId)
            ->where('id', $validated['cart_id'])
            ->delete();

        session()->flash('info', 'Product removed from cart');

        return redirect('/cart');
    }

    /**
     * Update the quantity of a product in the cart
     *
     * @method POST
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $product = Product::find($validated['product_id']);

        if (!$product || !$product->is_active) {
            session()->flash('error', 'Product not found or unavailable.');
            return redirect()->back();
        }

        if ($product->stock < $validated['quantity']) {
            session()->flash('error', 'Requested quantity exceeds available stock.');
            return redirect()->back();
        }

        $sessionId = $this->getSessionId();

        if ($validated['quantity'] === 0) {
            Cart::where('session_id', $sessionId)
                ->where('product_id', $validated['product_id'])
                ->delete();

            session()->flash('info', 'Product removed from cart');
        } else {
            Cart::where('session_id', $sessionId)
                ->where('product_id', $validated['product_id'])
                ->update(['quantity' => $validated['quantity']]);

            session()->flash('success', 'Cart updated successfully');
        }

        return redirect('/cart');
    }

    /**
     * Get or generate the cart session ID
     *
     * @method private
     * @return string
     */
    private function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            $uuid = (string) Str::uuid();
            session()->put('cart_session_id', $uuid);
        }

        return session()->get('cart_session_id');
    }
}