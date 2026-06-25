<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'before_stock',
        'after_stock',
        'reference_type',
        'reference_id',
        'notes',
    ];

    // Relasi ke produk
        public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }


    // Relasi polymorphic ke model reference
    public function reference()
    {
        return $this->morphTo();
    }

    // Scope untuk stok masuk
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    // Scope untuk stok keluar
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    // Method untuk mencatat stok masuk
    public static function recordStockIn(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): self
    {
        $beforeStock = $product->stock;
        $afterStock = $beforeStock + $quantity;

        // Update stok produk
        $product->stock = $afterStock;
        $product->save();

        // Catat pergerakan stok
        return self::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
        ]);
    }

    // Method untuk mencatat stok keluar
    public static function recordStockOut(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): self
    {
        $beforeStock = $product->stock;
        $afterStock = $beforeStock - $quantity;

        // Validasi stok cukup
        if ($afterStock < 0) {
            throw new \Exception("Stok tidak mencukupi untuk produk {$product->name}");
        }

        // Update stok produk
        $product->stock = $afterStock;
        $product->save();

        // Catat pergerakan stok
        return self::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
        ]);
    }
}
