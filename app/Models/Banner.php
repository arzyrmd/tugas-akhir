<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'description',
        'is_active',
    ];

    protected static function booted()
{
    static::saved(function () {
        event(new \App\Events\DataChanged());
    });

    static::deleted(function () {
        event(new \App\Events\DataChanged());
    });
}

}
