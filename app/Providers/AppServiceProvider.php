<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Ensures MySQL string length compatibility and shares global view data.
     *
     * @return void
     */
    public function boot(): void
    {
        // Fix MySQL < 5.7.7 compatibility for utf8mb4
        Schema::defaultStringLength(191);

        // Share cart count globally (available in all views)
        View::composer('*', function ($view) {
            $cartCount = 0;
            if (session()->has('cart_session_id')) {
                $cartClass = app\Models\Cart::class ?? null;
                if ($cartClass && class_exists($cartClass)) {
                    try {
                        $cartCount = $cartClass::where('session_id', session('cart_session_id'))->sum('quantity');
                    } catch (\Exception $e) {
                        $cartCount = 0;
                    }
                }
            }
            $view->with('cartCount', $cartCount);
        });
    }
}