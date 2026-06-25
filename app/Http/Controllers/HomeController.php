<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCategories = Category::where('is_featured', true)
            ->orderBy('sort_order')
            ->take(9)
            ->get();

        $categories = [];

        // Jika kategori featured kurang dari 9, tambahkan dengan kategori lain
        if ($featuredCategories->count() < 9) {
            $moreCategories = Category::where('is_featured', false)
                ->orderBy('sort_order')
                ->take(9 - $featuredCategories->count())
                ->get();

            $categories = $featuredCategories->merge($moreCategories);
        } else {
            $categories = $featuredCategories;
        }

        // Mengambil produk dengan harga terendah untuk setiap kategori
        foreach ($categories as $category) {
            $category->cheapestProduct = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('price', 'asc')
                ->first();
        }

        return view('home', compact('categories'));
    }
}
