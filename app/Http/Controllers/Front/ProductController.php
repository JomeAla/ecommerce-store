<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Frontend Product Controller
 *
 * Handles product listing, filtering, and detail pages
 *
 * @author E-commerce Starter Kit
 * @version 1.0.0
 */
class ProductController extends Controller
{
    /**
     * Display the shop page with product filtering and pagination
     *
     * @method GET
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::active();

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
            $activeCategory = $request->category;
        } else {
            $activeCategory = null;
        }

        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        } else {
            $searchTerm = null;
        }

        $sortBy = $request->get('sort', 'newest');

        switch ($sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);

        $categories = Product::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->sort();

        return view('front.shop', compact('products', 'categories', 'activeCategory', 'searchTerm', 'sortBy'));
    }

    /**
     * Display the product detail page
     *
     * @method GET
     * @param string $slug
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->first();

        if (!$product) {
            return abort(404);
        }

        $relatedProducts = Product::active()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('front.product', compact('product', 'relatedProducts', 'reviews'));
    }

    /**
     * Filter products by category and redirect to shop page
     *
     * @method GET
     * @param string $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function category($category)
    {
        return redirect()->to('/shop?category=' . urlencode($category));
    }
}