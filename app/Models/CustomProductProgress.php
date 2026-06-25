<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomProductProgress extends Model
{
    use HasFactory;
    protected $table = 'custom_product_progresses';

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

        public static function boot()
    {
        parent::boot();

        static::created(function ($progress) {
            $progress->load('customProductRequest.user');
            \App\Jobs\SendCustomProductProgressAddedNotification::dispatch($progress);
        });
    }

}
