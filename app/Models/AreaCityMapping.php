<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaCityMapping extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_area_id', 'city_id'];

    /**
     * Relasi dengan model DeliveryArea
     */
    public function deliveryArea()
    {
        return $this->belongsTo(DeliveryArea::class);
    }

    /**
     * Relasi dengan model City
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
