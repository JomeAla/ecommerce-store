<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminShippingZoneController;
use App\Http\Controllers\Admin\AdminShippingMethodController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/init', [CheckoutController::class, 'init'])->name('checkout.init');
Route::post('/checkout/shipping-rates', [CheckoutController::class, 'getShippingRates'])->name('checkout.shipping-rates');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/webhook/paystack', [CheckoutController::class, 'webhook'])->name('webhook.paystack');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/products', AdminProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    
    Route::get('/admin/shipping/zones', [AdminShippingZoneController::class, 'index'])->name('admin.shipping.zones');
    Route::post('/admin/shipping/zones', [AdminShippingZoneController::class, 'store'])->name('admin.shipping.zones.store');
    Route::patch('/admin/shipping/zones/{id}', [AdminShippingZoneController::class, 'update'])->name('admin.shipping.zones.update');
    Route::delete('/admin/shipping/zones/{id}', [AdminShippingZoneController::class, 'destroy'])->name('admin.shipping.zones.destroy');
    Route::post('/admin/shipping/zones/{id}/toggle', [AdminShippingZoneController::class, 'toggleActive'])->name('admin.shipping.zones.toggle');
    
    Route::get('/admin/shipping/methods', [AdminShippingMethodController::class, 'index'])->name('admin.shipping.methods');
    Route::post('/admin/shipping/methods', [AdminShippingMethodController::class, 'store'])->name('admin.shipping.methods.store');
    Route::patch('/admin/shipping/methods/{id}', [AdminShippingMethodController::class, 'update'])->name('admin.shipping.methods.update');
    Route::delete('/admin/shipping/methods/{id}', [AdminShippingMethodController::class, 'destroy'])->name('admin.shipping.methods.destroy');
    Route::post('/admin/shipping/methods/{id}/toggle', [AdminShippingMethodController::class, 'toggleActive'])->name('admin.shipping.methods.toggle');
});