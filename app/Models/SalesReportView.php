<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesReportView extends Model
{
    protected $table = 'sales_report_view';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $casts = [
        'total_amount' => 'decimal:2',
        'quoted_price' => 'decimal:2',
        'order_created_at' => 'datetime',
        'payment_date' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'dp_payment_date' => 'datetime',
        'full_payment_date' => 'datetime',
        'work_completed_at' => 'datetime',
    ];

    protected $fillable = [
        'unique_id',
        'original_id',
        'payment_code',
        'customer_name',
        'status',
        'total_amount',
        'order_created_at',
        'payment_date',
        'order_type',
        'user_id',
        'title',
        'description',
        'quoted_price',
        'dp_payment_date',
        'full_payment_date',
        'work_completed_at',
        'payment_status',
        'periode',
        'order_number'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Perbaikan relasi untuk order items
    public function orderItems(): HasMany
    {
        // Hanya untuk pesanan reguler
        if ($this->order_type === 'REGULER') {
            return $this->hasMany(OrderItem::class, 'order_id', 'original_id');
        }

        // Return empty relation untuk custom orders
        return $this->hasMany(OrderItem::class, 'order_id', 'original_id')->whereRaw('1 = 0');
    }

    // Getter untuk order items dengan eager loading
    public function getOrderItemsAttribute()
    {
        if ($this->order_type === 'REGULER') {
            // Load order items jika belum di-load
            if (!$this->relationLoaded('orderItems')) {
                $this->load('orderItems.product');
            }
            return $this->orderItems()->with('product')->get();
        }

        return collect();
    }

    // Accessor untuk product info - diperbaiki
    public function getProductInfoAttribute(): string
    {
        if ($this->order_type === 'CUSTOM') {
            return $this->title ?? 'Produk Kustom';
        }

        // Untuk pesanan reguler, ambil dari order items
        try {
            $orderItems = \App\Models\OrderItem::where('order_id', $this->original_id)
                ->with('product')
                ->get();

            if ($orderItems->isEmpty()) {
                return 'Tidak ada produk';
            }

            $products = $orderItems->map(function ($item) {
                $productName = $item->product ? $item->product->name : 'Produk tidak ditemukan';
                return $productName . ' (x' . $item->quantity . ')';
            })->take(2)->join(', ');

            if ($orderItems->count() > 2) {
                $products .= ' (+' . ($orderItems->count() - 2) . ' lainnya)';
            }

            return $products;
        } catch (\Exception $e) {
            return 'Error loading products';
        }
    }

    // Accessor untuk profit margin - diperbaiki
    public function getProfitMarginAttribute(): float
    {
        if ($this->order_type === 'CUSTOM') {
            $revenue = $this->quoted_price ?? 0;
            $estimatedCost = $revenue * 0.6; // 40% margin assumption
            return $revenue - $estimatedCost;
        }

        // Untuk pesanan reguler
        try {
            $revenue = $this->total_amount ?? 0;

            $orderItems = \App\Models\OrderItem::where('order_id', $this->original_id)
                ->with('product')
                ->get();

            $cost = $orderItems->sum(function ($item) {
                $costPrice = $item->product ? ($item->product->cost_price ?? 0) : 0;
                return $costPrice * $item->quantity;
            });

            return $revenue - $cost;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Method untuk mendapatkan detail produk
    public function getProductDetails()
    {
        if ($this->order_type === 'CUSTOM') {
            return [
                'type' => 'custom',
                'title' => $this->title,
                'description' => $this->description,
                'items' => []
            ];
        }

        $orderItems = \App\Models\OrderItem::where('order_id', $this->original_id)
            ->with('product')
            ->get();

        return [
            'type' => 'regular',
            'title' => null,
            'description' => null,
            'items' => $orderItems->map(function ($item) {
                return [
                    'product_name' => $item->product ? $item->product->name : 'Produk tidak ditemukan',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->quantity * $item->price
                ];
            })
        ];
    }

    // Scope untuk filtering
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_created_at', [$startDate, $endDate]);
    }

    public function scopeByOrderType($query, $type)
    {
        return $query->where('order_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    // Method untuk mendapatkan statistik
    public static function getStatistics($filters = [])
    {
        $query = self::query();

        // Apply filters
        if (isset($filters['start_date'])) {
            $query->where('order_created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('order_created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['order_type'])) {
            $query->where('order_type', $filters['order_type']);
        }

        $data = $query->get();

        return [
            'total_orders' => $data->count(),
            'total_revenue' => $data->sum('total_amount'),
            'orders_by_type' => $data->groupBy('order_type')->map->count(),
            'orders_by_status' => $data->groupBy('payment_status')->map->count(),
            'average_order_value' => $data->avg('total_amount'),
        ];
    }
}
