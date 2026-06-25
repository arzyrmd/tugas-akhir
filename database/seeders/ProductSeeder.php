<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Buat atau ambil kategori dummy dengan slug
        $category = Category::firstOrCreate(
            ['name' => 'Dummy Kategori'],
            ['slug' => 'dummy-kategori']
        );

        // Hapus produk dummy lama
        Product::where('name', 'like', 'Produk Dummy%')->delete();

        // Buat 5 produk dummy
        for ($i = 1; $i <= 5; $i++) {
            Product::create([
                'name' => "Produk Dummy $i",
                'slug' => Str::slug("Produk Dummy $i"), // slug wajib
                'description' => "Deskripsi untuk produk dummy $i",
                'price' => rand(20000, 50000),
                'stock' => rand(5, 20),
                'image' => 'dummy.jpg',
                'category_id' => $category->id,
            ]);
        }

        $this->command->info('✅ 5 produk dummy berhasil dibuat.');
    }
}
