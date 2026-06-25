<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryArea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    /**
     * Relasi many-to-many dengan model City
     */
    public function cities()
    {
        return $this->belongsToMany(City::class, 'area_city_mappings');
    }

    /**
     * Mendapatkan semua pesanan dalam area ini
     */
    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            AreaCityMapping::class,
            'delivery_area_id', // Kunci asing pada area_city_mappings
            'city_id',          // Kunci asing pada orders
            'id',               // Kunci lokal pada delivery_areas
            'city_id'           // Kunci lokal pada area_city_mappings
        );
    }

    /**
     * Mendapatkan pesanan dengan status "DIKEMAS" yang belum punya tanggal pengiriman
     */
    public function pendingOrders()
    {
        return $this->orders()
            ->where('status', 'DIKEMAS')
            ->whereNull('delivery_date');
    }
}
