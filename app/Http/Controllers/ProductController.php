<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil semua kategori
        $categories = Category::orderBy('sort_order')->get();

        // Mendapatkan kategori yang dipilih jika ada
        $categorySlug = $request->query('category');
        $selectedCategory = null;

        // Query dasar produk
        $productsQuery = Product::where('is_active', true);

        // Filter berdasarkan kategori jika ada
        if ($categorySlug) {
            $selectedCategory = Category::where('slug', $categorySlug)->first();
            if ($selectedCategory) {
                $productsQuery->where('category_id', $selectedCategory->id);
            }
        }

        // Filter berdasarkan rentang harga jika ada
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');

        if ($minPrice) {
            $productsQuery->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // Pengurutan produk
        $sort = $request->query('sort', 'newest');

        switch ($sort) {
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'popular':
                // Default ke newest jika tidak ada kolom views
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        // Paginasi - Ubah default menjadi 4 produk per halaman
        $perPage = $request->query('per_page', 4); // Ubah dari 12 menjadi 4
        $products = $productsQuery->paginate($perPage);

        return view('products.index', compact('categories', 'products', 'selectedCategory', 'sort', 'perPage', 'minPrice', 'maxPrice'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // Jika keyword kosong, kembali ke halaman produk
        if (empty($keyword)) {
            return redirect()->route('products.index');
        }

        // Mengambil semua kategori untuk sidebar
        $categories = Category::orderBy('sort_order')->get();

        // Cari produk berdasarkan keyword
        $productsQuery = Product::where(function ($query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        })
            ->where('is_active', true);

        // Filter harga jika ada
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');

        if ($minPrice) {
            $productsQuery->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // Pengurutan produk
        $sort = $request->query('sort', 'newest');

        switch ($sort) {
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'popular':
                // Default ke newest jika tidak ada kolom views
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        // Paginasi - Ubah default menjadi 4 produk per halaman
        $perPage = $request->query('per_page', 4); // Ubah dari 12 menjadi 4
        $products = $productsQuery->paginate($perPage);

        // Mengatur parameter untuk disetel kembali saat pagination
        $products->appends([
            'keyword' => $keyword,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
            'per_page' => $perPage,
        ]);

        return view('products.index', compact(
            'categories',
            'products',
            'keyword',
            'sort',
            'perPage',
            'minPrice',
            'maxPrice'
        ));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $category = Category::find($product->category_id);

        // Ambil produk terkait dari kategori yang sama
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'category', 'relatedProducts'));
    }

    // Method lainnya tetap sama
}
