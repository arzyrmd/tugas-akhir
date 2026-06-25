<?php

namespace App\Models;

use App\Jobs\SendNewCustomRequestNotification;
use App\Jobs\SendCustomRequestStatusUpdatedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CustomProductRequest extends Model
{
    use HasFactory;

    // Konstanta untuk status request
    const STATUS_PENDING = 'pending';
    const STATUS_QUOTED = 'quoted';
    const STATUS_DP_PAID = 'dp_paid';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'specifications',
        'budget',
        'desired_deadline',
        'status',
        'quoted_price',
        'down_payment',
        'remaining_payment',
        'estimated_completion',
        'admin_notes',
        'dp_payment_code',
        'dp_payment_date',
        'full_payment_code',
        'full_payment_date',
        'work_started_at',
        'work_completed_at',
        'shipping_date',
        'delivery_date',

    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'quoted_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'remaining_payment' => 'decimal:2',
        'desired_deadline' => 'date',
        'estimated_completion' => 'date',
        'dp_payment_date' => 'datetime',
        'full_payment_date' => 'datetime',
        'work_started_at' => 'date',
        'work_completed_at' => 'date',
        'shipping_date' => 'date',
        'delivery_date' => 'date',
    ];

    // Accessor untuk mendapatkan daftar status yang valid
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_QUOTED,
            self::STATUS_DP_PAID,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
            self::STATUS_REJECTED,
        ];
    }


     public function finalProduct(): HasOne
    {
        return $this->hasOne(CustomProductFinal::class);
    }
    /**
     * Mendapatkan pemilik request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan foto referensi
     */
    public function references(): HasMany
    {
        return $this->hasMany(CustomProductReference::class);
    }

    /**
     * Mendapatkan foto progres pengerjaan
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(CustomProductProgress::class);
    }

    /**
     * Mendapatkan informasi pengiriman
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(CustomProductShipment::class);
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

    // Scope untuk request dalam rentang budget
    public function scopeInBudgetRange($query, $minBudget, $maxBudget)
    {
        return $query->whereBetween('budget', [$minBudget, $maxBudget]);
    }

    // Scope untuk request dengan deadline mendekati
    public function scopeUpcomingDeadline($query, $days = 7)
    {
        return $query->where('desired_deadline', '<=', now()->addDays($days))
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    // Method untuk mengecek apakah request bisa dibatalkan
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_QUOTED
        ]);
    }

    // Method untuk mengecek apakah sudah ada quotation
    public function hasQuotation(): bool
    {
        return !is_null($this->quoted_price) && $this->status !== self::STATUS_PENDING;
    }

    // Method untuk mengecek apakah DP sudah dibayar
    public function isDpPaid(): bool
    {
        return !is_null($this->dp_payment_date) && !is_null($this->dp_payment_code);
    }

    // Method untuk mengecek apakah pembayaran penuh sudah selesai
    public function isFullyPaid(): bool
    {
        return !is_null($this->full_payment_date) && !is_null($this->full_payment_code);
    }

    // Method untuk menghitung persentase progress
    public function getProgressPercentageAttribute(): int
    {
        $statusProgress = [
            self::STATUS_PENDING => 0,
            self::STATUS_QUOTED => 20,
            self::STATUS_DP_PAID => 40,
            self::STATUS_IN_PROGRESS => 60,
            self::STATUS_COMPLETED => 80,
            self::STATUS_SHIPPED => 90,
            self::STATUS_DELIVERED => 100,
            self::STATUS_CANCELLED => 0,
            self::STATUS_REJECTED => 0,
        ];

        return $statusProgress[$this->status] ?? 0;
    }

    // Method untuk mendapatkan status dalam bahasa Indonesia
    public function getStatusLabelAttribute(): string
    {
        $statusLabels = [
            self::STATUS_PENDING => 'Menunggu Quotation',
            self::STATUS_QUOTED => 'Sudah Ada Quotation',
            self::STATUS_DP_PAID => 'DP Sudah Dibayar',
            self::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
            self::STATUS_COMPLETED => 'Selesai Dikerjakan',
            self::STATUS_SHIPPED => 'Sedang Dikirim',
            self::STATUS_DELIVERED => 'Sudah Diterima',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_REJECTED => 'Ditolak',
        ];

        return $statusLabels[$this->status] ?? 'Status Tidak Dikenal';
    }

    // Method untuk menghitung sisa hari hingga deadline
    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->desired_deadline) {
            return null;
        }

        return now()->diffInDays($this->desired_deadline, false);
    }

    // Method untuk mengecek apakah deadline sudah terlewat
    public function isOverdueAttribute(): bool
    {
        if (!$this->desired_deadline) {
            return false;
        }

        return now()->isAfter($this->desired_deadline) &&
               !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_DELIVERED]);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($request) {
            $request->load('user');
            SendNewCustomRequestNotification::dispatch($request);
        });

        static::updating(function ($request) {
            if ($request->isDirty('status')) {
                $oldStatus = $request->getOriginal('status');
                SendCustomRequestStatusUpdatedNotification::dispatch($request, $oldStatus);
            }
        });
    }
}
