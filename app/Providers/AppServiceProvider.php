<?php

namespace App\Providers;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Models\DeliveryBatch;
use App\Observers\DeliveryBatchObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
            public function boot()
        {
            RateLimiter::for('login', function (Request $request) {
                return Limit::perMinute(5)->by($request->email . '|' . $request->ip());
            });

            Carbon::setLocale('id');
View::composer('home', \App\Http\View\Composers\HomeComposer::class);
            // Membuat format hari dalam bahasa Indonesia
            setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id');
         OrderItem::observe(OrderItemObserver::class);
             DeliveryBatch::observe(DeliveryBatchObserver::class);
             Product::observe(ProductObserver::class);
        }
}
