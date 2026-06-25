<?php

namespace App\Models;

use App\Jobs\SendNewOrderNotificationToAdmins;
use App\Jobs\SendOrderStatusUpdatedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    use HasFactory;

    // Konstanta untuk status order
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'address',
        'province_id',
        'city_id',
        'postal_code',
        'notes',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'payment_method',
        'payment_code',
        'order_created_at',
        'payment_date',
        'packing_date',
        'delivery_date',
        'stock_returned',
        'completed_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'stock_returned' => 'boolean',
        'order_created_at' => 'datetime',
        'payment_date' => 'datetime',
        'packing_date' => 'datetime',
        'delivery_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    // Accessor untuk mendapatkan daftar status yang valid
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    // Relasi yang sudah ada tetap dipertahankan
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relasi ke delivery_items
     */
    public function deliveryItems(): MorphMany
    {
        return $this->morphMany(DeliveryItem::class, 'deliverable');
    }

    /**
     * Helper method untuk mendapatkan batch pengiriman
     */
    public function getDeliveryBatch()
    {
        return $this->deliveryItems()->first()?->batch;
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk order dalam rentang tanggal
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_created_at', [$startDate, $endDate]);
    }

    // Method untuk mengecek apakah order bisa dibatalkan
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAID]);
    }

    // Method untuk mengecek apakah order sudah selesai
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    // Method untuk menghitung total item
    public function getTotalItemsAttribute(): int
    {
        return $this->orderItems()->sum('quantity');
    }

    // Method untuk mendapatkan status dalam bahasa Indonesia
    public function getStatusLabelAttribute(): string
    {
        $statusLabels = [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Sudah Dibayar',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_DELIVERED => 'Terkirim',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];

        return $statusLabels[$this->status] ?? 'Status Tidak Dikenal';
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($order) {
            SendNewOrderNotificationToAdmins::dispatch($order);
        });

        static::updating(function ($order) {
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                SendOrderStatusUpdatedNotification::dispatch($order, $oldStatus);
            }
        });
    }
}
