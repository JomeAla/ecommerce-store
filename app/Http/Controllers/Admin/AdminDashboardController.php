<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Admin Dashboard Controller
 *
 * Handles dashboard statistics and overview data
 *
 * @author E-commerce Starter Kit
 * @version 1.0.0
 */
class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with key statistics.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $stats = [
            'total_orders' => $this->getTotalOrders(),
            'today_orders' => $this->getTodayOrders($today),
            'revenue' => $this->getTotalRevenue(),
            'today_revenue' => $this->getTodayRevenue($today),
            'pending_orders' => $this->getPendingOrders(),
            'total_customers' => $this->getTotalCustomers(),
            'new_customers_this_month' => $this->getNewCustomersThisMonth(),
            'total_products' => $this->getTotalProducts(),
            'active_products' => $this->getActiveProducts(),
            'featured_products' => $this->getFeaturedProducts(),
            'low_stock_products' => $this->getLowStockProducts(),
        ];

        $recentOrders = $this->getRecentOrders();
        $topProducts = $this->getTopProducts();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'topProducts'
        ));
    }

    /**
     * Get total number of orders.
     *
     * @return int
     */
    private function getTotalOrders(): int
    {
        $orderClass = $this->getOrderClass();
        if (!$orderClass) {
            return 0;
        }

        try {
            return $orderClass::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get number of orders created today.
     *
     * @param string $today
     * @return int
     */
    private function getTodayOrders(string $today): int
    {
        $orderClass = $this->getOrderClass();
        if (!$orderClass) {
            return 0;
        }

        try {
            return $orderClass::whereDate('created_at', $today)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total revenue from paid orders.
     *
     * @return float
     */
    private function getTotalRevenue(): float
    {
        $orderClass = $this->getOrderClass();
        if (!$orderClass) {
            return 0.0;
        }

        try {
            return $orderClass::where('payment_status', 'paid')
                ->sum('total_amount') ?? 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get today's revenue from paid orders.
     *
     * @param string $today
     * @return float
     */
    private function getTodayRevenue(string $today): float
    {
        $orderClass = $this->getOrderClass();
        if (!$orderClass) {
            return 0.0;
        }

        try {
            return $orderClass::where('payment_status', 'paid')
                ->whereDate('created_at', $today)
                ->sum('total_amount') ?? 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get number of orders with pending payment status.
     *
     * @return int
     */
    private function getPendingOrders(): int
    {
        $orderClass = $this->getOrderClass();
        if (!$orderClass) {
            return 0;
        }

        try {
            return $orderClass::where('payment_status', 'pending')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the 10 most recent orders with customer details.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    private function getRecentOrders()
    {
        $orderClass = $this->getOrderClass();
        if (!$orderClass) {
            return [];
        }

        try {
            return $orderClass::with(['customer', 'order'])
                ->latest()
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Dashboard recent orders error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top 5 products by order count.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    private function getTopProducts()
    {
        $productClass = $this->getProductClass();
        if (!$productClass) {
            return [];
        }

        try {
            return $productClass::withCount(['orders as orders_count' => function ($query) {
                    $query->where('payment_status', 'paid');
                }])
                ->withSum(['orders as revenue' => function ($query) {
                    $query->where('payment_status', 'paid');
                }], 'total_amount')
                ->orderByDesc('orders_count')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::error('Dashboard top products error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total number of customers.
     *
     * @return int
     */
    private function getTotalCustomers(): int
    {
        $userClass = $this->getUserClass();
        if (!$userClass) {
            return 0;
        }

        try {
            return $userClass::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get number of new customers this month.
     *
     * @return int
     */
    private function getNewCustomersThisMonth(): int
    {
        $userClass = $this->getUserClass();
        if (!$userClass) {
            return 0;
        }

        try {
            return $userClass::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total number of products.
     *
     * @return int
     */
    private function getTotalProducts(): int
    {
        $productClass = $this->getProductClass();
        if (!$productClass) {
            return 0;
        }

        try {
            return $productClass::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get number of active products.
     *
     * @return int
     */
    private function getActiveProducts(): int
    {
        $productClass = $this->getProductClass();
        if (!$productClass) {
            return 0;
        }

        try {
            return $productClass::where('is_active', true)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get number of featured products.
     *
     * @return int
     */
    private function getFeaturedProducts(): int
    {
        $productClass = $this->getProductClass();
        if (!$productClass) {
            return 0;
        }

        try {
            return $productClass::where('is_featured', true)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get number of products with low stock.
     *
     * @return int
     */
    private function getLowStockProducts(): int
    {
        $productClass = $this->getProductClass();
        if (!$productClass) {
            return 0;
        }

        try {
            return $productClass::where('track_stock', true)
                ->where('stock', '<=', 5)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the Order model class name.
     *
     * @return string|null
     */
    private function getOrderClass(): ?string
    {
        if (class_exists('App\\Models\\Order')) {
            return 'App\\Models\\Order';
        }
        if (class_exists('App\\Order')) {
            return 'App\\Order';
        }
        return null;
    }

    /**
     * Get the Product model class name.
     *
     * @return string|null
     */
    private function getProductClass(): ?string
    {
        if (class_exists('App\\Models\\Product')) {
            return 'App\\Models\\Product';
        }
        if (class_exists('App\\Product')) {
            return 'App\\Product';
        }
        return null;
    }

    /**
     * Get the User model class name.
     *
     * @return string|null
     */
    private function getUserClass(): ?string
    {
        if (class_exists('App\\Models\\Customer')) {
            return 'App\\Models\\Customer';
        }
        if (class_exists('App\\Models\\User')) {
            return 'App\\Models\\User';
        }
        return null;
    }
}