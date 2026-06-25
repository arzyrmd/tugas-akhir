<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomProductFinal extends Model
{
    use HasFactory;

    protected $table = 'custom_product_finals';

    protected $fillable = [
        'custom_product_request_id',
        'image_path',
        'notes'
    ];

    /**
     * Mendapatkan request terkait
     */
    public function customProductRequest(): BelongsTo
    {
        return $this->belongsTo(CustomProductRequest::class);
    }

    /**
     * Boot method untuk event handling
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($final) {
            $final->load('customProductRequest.user');
            // Dispatch notification job jika diperlukan
            // \App\Jobs\SendCustomProductFinalUploadedNotification::dispatch($final);
        });
    }
}
