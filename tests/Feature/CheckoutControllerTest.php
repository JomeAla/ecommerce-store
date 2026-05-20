<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Checkout Controller Feature Tests
 *
 * Tests the checkout flow including cart validation and payment initialization
 *
 * @author E-commerce Starter Kit
 * @version 1.0.0
 */
class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that checkout redirects to cart when cart is empty
     *
     * @return void
     */
    public function test_checkout_redirects_to_cart_when_empty()
    {
        $this->withoutMiddleware();

        $response = $this->get('/checkout');

        $response->assertRedirect('/cart');
        $response->assertSessionHas('info');
    }

    /**
     * Test that checkout page displays when cart has items
     *
     * @return void
     */
    public function test_checkout_page_displays_with_cart_items()
    {
        $this->withoutMiddleware();

        $sessionId = 'test-session-' . uniqid();
        $this->withSession(['cart_session_id' => $sessionId]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test Description',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
            'is_featured' => false,
            'category' => 'Test Category',
        ]);

        Cart::create([
            'session_id' => $sessionId,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->get('/checkout');

        $response->assertStatus(200);
        $response->assertViewIs('front.checkout');
    }

    /**
     * Test payment initialization returns Paystack URL on valid request
     *
     * @return void
     */
    public function test_payment_init_returns_paystack_url_on_valid_request()
    {
        $this->withoutMiddleware();

        $sessionId = 'test-session-' . uniqid();
        $this->withSession(['cart_session_id' => $sessionId]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product-checkout',
            'description' => 'Test Description',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
            'is_featured' => false,
            'category' => 'Test Category',
        ]);

        Cart::create([
            'session_id' => $sessionId,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->mock(\App\Services\PaystackService::class, function ($mock) {
            $mock->shouldReceive('initializePayment')
                ->once()
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'reference' => 'TEST-REF-123',
                        'authorization_url' => 'https://paystack.com/pay/test-url',
                    ],
                ]);
        });

        $response = $this->post('/checkout/init', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'url',
        ]);
    }

    /**
     * Test payment initialization fails with empty cart
     *
     * @return void
     */
    public function test_payment_init_fails_with_empty_cart()
    {
        $this->withoutMiddleware();

        $sessionId = 'test-session-' . uniqid();
        $this->withSession(['cart_session_id' => $sessionId]);

        $response = $this->post('/checkout/init', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }

    /**
     * Test payment initialization fails with invalid customer data
     *
     * @return void
     */
    public function test_payment_init_fails_with_invalid_data()
    {
        $this->withoutMiddleware();

        $sessionId = 'test-session-' . uniqid();
        $this->withSession(['cart_session_id' => $sessionId]);

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product-invalid',
            'description' => 'Test Description',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
            'is_featured' => false,
            'category' => 'Test Category',
        ]);

        Cart::create([
            'session_id' => $sessionId,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->post('/checkout/init', [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /**
     * Test that checkout creates order on successful payment initialization
     *
     * @return void
     */
    public function test_checkout_creates_order_on_payment_init()
    {
        $this->withoutMiddleware();

        $sessionId = 'test-session-' . uniqid();
        $this->withSession(['cart_session_id' => $sessionId]);

        $product = Product::create([
            'name' => 'Test Product for Order',
            'slug' => 'test-product-order',
            'description' => 'Test Description',
            'price' => 250.00,
            'stock' => 5,
            'is_active' => true,
            'is_featured' => false,
            'category' => 'Electronics',
        ]);

        Cart::create([
            'session_id' => $sessionId,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->mock(\App\Services\PaystackService::class, function ($mock) {
            $mock->shouldReceive('initializePayment')
                ->once()
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'reference' => 'ORD-20240101-TEST123',
                        'authorization_url' => 'https://paystack.com/pay/test',
                    ],
                ]);
        });

        $this->post('/checkout/init', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08012345678',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'john@example.com',
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => 'Test Product for Order',
            'quantity' => 2,
        ]);
    }

    /**
     * Test checkout success page displays with valid order
     *
     * @return void
     */
    public function test_checkout_success_page_displays_with_valid_order()
    {
        $this->withoutMiddleware();

        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '08012345678',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-20240101-TEST',
            'download_token' => 'test-token-123',
            'subtotal' => 500.00,
            'discount' => 0,
            'total' => 500.00,
            'status' => 'processing',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@test.com',
            'customer_phone' => '08012345678',
        ]);

        $response = $this->get('/checkout/success?reference=' . $order->order_number);

        $response->assertStatus(200);
        $response->assertViewIs('front.checkout_success');
        $response->assertViewHas('order');
    }

    /**
     * Test webhook updates order status on successful payment
     *
     * @return void
     */
    public function test_webhook_updates_order_on_successful_payment()
    {
        $this->withoutMiddleware();

        $customer = Customer::create([
            'name' => 'Webhook Test',
            'email' => 'webhook@test.com',
            'phone' => '08012345678',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-WEBHOOK-TEST',
            'download_token' => 'webhook-token',
            'subtotal' => 300.00,
            'discount' => 0,
            'total' => 300.00,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_reference' => 'PAY-REF-123',
            'customer_name' => 'Webhook Test',
            'customer_email' => 'webhook@test.com',
            'customer_phone' => '08012345678',
        ]);

        $response = $this->post('/checkout/webhook', [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'PAY-REF-123',
                'status' => 'success',
            ],
        ], ['X-Paystack-Event' => 'charge.success']);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->paid_at);
    }

    /**
     * Test cart is cleared after successful checkout
     *
     * @return void
     */
    public function test_cart_cleared_after_successful_checkout()
    {
        $this->withoutMiddleware();

        $sessionId = 'test-session-clear';
        $this->withSession(['cart_session_id' => $sessionId]);

        $product = Product::create([
            'name' => 'Product to Clear',
            'slug' => 'product-clear',
            'description' => 'Test',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
            'is_featured' => false,
            'category' => 'Test',
        ]);

        Cart::create([
            'session_id' => $sessionId,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertEquals(1, Cart::where('session_id', $sessionId)->count());

        $this->mock(\App\Services\PaystackService::class, function ($mock) {
            $mock->shouldReceive('initializePayment')
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'reference' => 'CLEAR-REF',
                        'authorization_url' => 'https://paystack.com/pay/test',
                    ],
                ]);
            $mock->shouldReceive('verifyPayment')
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'status' => 'success',
                        'reference' => 'CLEAR-REF',
                    ],
                ]);
        });

        $this->post('/checkout/init', [
            'name' => 'Test User',
            'email' => 'clear@test.com',
            'phone' => '08012345678',
        ]);

        $this->get('/checkout/success?reference=ORD-20240101-XXX');

        $this->assertEquals(0, Cart::where('session_id', $sessionId)->count());
    }
}