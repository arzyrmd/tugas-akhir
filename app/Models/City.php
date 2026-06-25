<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['province_id', 'name', 'code', 'shipping_cost'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function deliveryAreas()
    {
        return $this->belongsToMany(DeliveryArea::class, 'area_city_mappings');
    }


}
