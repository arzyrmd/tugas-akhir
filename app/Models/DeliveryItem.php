<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeliveryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_batch_id',
        'deliverable_type',
        'deliverable_id',
        'status',
        'delivered_at'
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    /**
     * Relasi ke batch pengiriman
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(DeliveryBatch::class, 'delivery_batch_id');
    }

    /**
     * Relasi polymorphic ke pesanan/produk kustom
     */
    public function deliverable(): MorphTo
    {
        return $this->morphTo();
    }
}
