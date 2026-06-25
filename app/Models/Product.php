<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'gallery',
        'is_featured',
        'is_active',
        'weight',
        'length',
        'width',
        'height',
        'material',
        'stock'
    ];

    protected $casts = [
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Method untuk menghapus file gambar utama.
     */
    public function deleteMainImage()
    {
        if ($this->image) {
            Storage::disk('public')->delete($this->image);
        }
    }

    /**
     * Method untuk menghapus file gambar galeri dari storage.
     */
    public function deleteGalleryImages()
    {
        if (is_array($this->gallery)) {
            foreach ($this->gallery as $image) {
                Storage::disk('public')->delete($image);
            }

            // Update field gallery menjadi null
            $this->update(['gallery' => null]);
        }
    }

    /**
     * Method untuk menghapus file gambar utama dan galeri.
     */
    public function deleteImage()
    {
        $this->deleteMainImage();

        if (is_array($this->gallery)) {
            foreach ($this->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }
    }


    /**
     * Event model untuk menghapus file saat product dihapus dari database.
     */
    protected static function booted()
    {
        static::deleting(function ($product) {
            $product->deleteImage(); // panggil method deleteImage() saat delete
        });


    }

    // In your Product model
       // In your Product model
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
    public function stockMovements()
{
    return $this->hasMany(StockMovement::class);
}

}
