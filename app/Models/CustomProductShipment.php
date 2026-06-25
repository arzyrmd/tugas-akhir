<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomProductShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_product_request_id',
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
        'tracking_number'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Mendapatkan request terkait
     */
    public function customProductRequest(): BelongsTo
    {
        return $this->belongsTo(CustomProductRequest::class);
    }

    /**
     * Mendapatkan provinsi
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Mendapatkan kota
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
