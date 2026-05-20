<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'stock',
        'track_stock',
        'weight',
        'dimensions',
        'category',
        'image',
        'images',
        'file_path',
        'file_name',
        'product_type',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'track_stock' => 'boolean',
        'images' => 'json',
        'dimensions' => 'json',
        'meta_title' => 'json',
        'meta_description' => 'json',
        'meta_keywords' => 'json',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_id');
    }

    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->isOnSale()) {
            return 0;
        }

        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->current_price, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function hasStock(): bool
    {
        if (!$this->track_stock) {
            return true;
        }

        return $this->stock > 0;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function decrementStock(int $quantity): bool
    {
        if (!$this->track_stock) {
            return true;
        }

        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }

        return false;
    }
}