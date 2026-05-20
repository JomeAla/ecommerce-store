<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = Product::distinct()->pluck('category')->filter()->sort();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter()->sort();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        if (!isset($validated['track_stock'])) {
            $validated['track_stock'] = false;
        }

        if (empty($validated['product_type'])) {
            $validated['product_type'] = 'digital';
        }

        if (!isset($validated['stock'])) {
            $validated['stock'] = 999;
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image'] = $path;
        }

        if (isset($validated['dimensions']) && is_array($validated['dimensions'])) {
            $validated['dimensions'] = array_filter($validated['dimensions']);
            if (empty($validated['dimensions'])) {
                $validated['dimensions'] = null;
            }
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Product::distinct()->pluck('category')->filter()->sort();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        if (!isset($validated['track_stock'])) {
            $validated['track_stock'] = false;
        }

        if ($request->hasFile('image_file')) {
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image'] = $path;
        }

        if (isset($validated['dimensions']) && is_array($validated['dimensions'])) {
            $validated['dimensions'] = array_filter($validated['dimensions']);
            if (empty($validated['dimensions'])) {
                $validated['dimensions'] = null;
            }
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}