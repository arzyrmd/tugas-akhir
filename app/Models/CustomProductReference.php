<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomProductReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_product_request_id',
        'image_path',
        'description'
    ];

    /**
     * Mendapatkan request terkait
     */
    public function customProductRequest(): BelongsTo
    {
        return $this->belongsTo(CustomProductRequest::class);
    }
}
