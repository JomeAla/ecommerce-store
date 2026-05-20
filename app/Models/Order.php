<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'discount_amount',
        'total_amount',
        'coupon_code',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'download_token',
        'download_count',
        'download_expires_at',
        'cart_data',
        'ip_address',
        'user_agent',
        'notes',
        'shipping_zone_id',
        'shipping_method',
        'shipping_cost',
        'shipping_address',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'download_count' => 'integer',
        'paid_at' => 'datetime',
        'download_expires_at' => 'datetime',
        'cart_data' => 'json',
        'shipping_address' => 'json',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'ORD-' . $date . '-';

        $lastOrder = static::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    public static function generateDownloadToken(): string
    {
        return Str::random(60);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function canDownload(): bool
    {
        if (!$this->isPaid() || empty($this->download_token)) {
            return false;
        }

        if ($this->download_expires_at && now()->greaterThan($this->download_expires_at)) {
            return false;
        }

        $product = $this->order;
        return $product && !empty($product->file_path);
    }

    public function getFormatTotalAttribute(): string
    {
        return number_format($this->total_amount, 2);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }
}