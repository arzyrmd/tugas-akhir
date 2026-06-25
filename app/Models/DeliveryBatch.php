<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DeliveryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_date',
        'delivery_area_id',
        'driver_name',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    /**
     * Relasi ke area pengiriman
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(DeliveryArea::class, 'delivery_area_id');
    }

    /**
     * Relasi ke item pengiriman
     */
    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    /**
     * Pesanan reguler dalam batch ini
     */
    public function orders(): MorphToMany
    {
        return $this->morphedByMany(Order::class, 'deliverable', 'delivery_items')
            ->where('orders.status', '!=', 'SELESAI'); // Tambahkan nama tabel di sini
    }

    /**
     * Produk kustom dalam batch ini
     */
    public function customProducts(): MorphToMany
    {
        return $this->morphedByMany(CustomProductRequest::class, 'deliverable', 'delivery_items')
            ->where('custom_product_requests.status', '!=', 'SELESAI'); // Tambahkan nama tabel di sini
    }
}
