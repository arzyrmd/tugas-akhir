<?php

// ===== LANGKAH 1: Buat View Composer Class =====
// File: app/Http/View/Composers/HomeComposer.php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Category;

class HomeComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Ambil data categories
        $categories = Category::with(['products' => function($query) {
            $query->orderBy('price', 'asc')->limit(1);
        }])->get();

        // Kirim data ke view
        $view->with('categories', $categories);
    }
}
