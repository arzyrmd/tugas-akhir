<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Model;

// app/Models/Category.php
class Category extends Model
{


    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'is_featured',
        'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function deleteImage()
    {
        if ($this->image) {
            Storage::disk('public')->delete($this->image);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            $category->deleteImage();
        });
    }
}
