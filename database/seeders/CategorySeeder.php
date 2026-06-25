<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan direktori storage/app/public/category ada
        $storagePath = storage_path('app/public/category');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Daftar kategori furniture (sofa dan kursi dipisah)
        $categories = [
            'Sofa',
            'Kursi',
            'Meja',
            'Lemari',
            'Tempat Tidur',
            'Rak',
            'Perlengkapan Dapur',
            'Dekorasi Rumah',
            'Furniture Outdoor',
            'Kursi Kantor',
            'Meja Belajar'
        ];

        // Tentukan kategori yang akan menjadi featured (maks 9)
        $categoryCount = count($categories);
        $featuredCount = min(9, $categoryCount);
        $indexes = range(0, $categoryCount - 1);
        shuffle($indexes);
        $featuredIndexes = array_slice($indexes, 0, $featuredCount);

        foreach ($categories as $index => $name) {
            $slug = Str::slug($name);
            $imageNumber = ($index % 5) + 1;
            $imageName = "category-{$imageNumber}.jpg";

            // Dummy image copy (opsional)
            // $sourcePath = database_path('seeders/dummy-images/' . $imageName);
            // $destPath = $storagePath . '/' . $imageName;
            // if (File::exists($sourcePath) && !File::exists($destPath)) {
            //     File::copy($sourcePath, $destPath);
            // }

            Category::create([
                'name' => $name,
                'slug' => $slug,
                'image' => $imageName,
                'is_featured' => in_array($index, $featuredIndexes),
                'sort_order' => $index + 1,
            ]);
        }

        $this->command->info('Categories have been created successfully!');
        $this->command->info("$featuredCount categories have been set as featured!");
    }
}
