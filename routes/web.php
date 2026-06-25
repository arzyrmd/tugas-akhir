<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProductCustomController;
use App\Http\Controllers\DeliveryManifestController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InvoiceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Di sinilah semua rute web didefinisikan.
| File ini bertugas menghubungkan user dengan controller yang sesuai
| berdasarkan URL dan aksi yang diminta.
|
| Semua rute di sini otomatis mendapat middleware "web".
*/

/*
|--------------------------------------------------------------------------
| Public Routes - Tanpa login
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Login dengan throttle (maks 3x/menit)
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:3,1');

Route::get('/invoice/{order}/print', [InvoiceController::class, 'print'])
    ->name('invoice.print')
    ->middleware('auth');

// Route untuk invoice custom product DP
Route::get('/invoice/custom/{customRequest}/dp', [InvoiceController::class, 'printCustomDpInvoice'])
    ->name('invoice.custom.dp')
    ->middleware('auth');

// Route untuk invoice custom product full payment
Route::get('/invoice/custom/{customRequest}/full', [InvoiceController::class, 'printCustomFullInvoice'])
    ->name('invoice.custom.full')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Setelah login & verifikasi
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
   Route::get('/custom/{id}/payment-dp-success', [ProductCustomController::class, 'paymentDpSuccess'])
    ->name('custom.payment-dp-success');
    Route::get('/custom/{id}/payment-full-success', [ProductCustomController::class, 'paymentFullSuccess'])
    ->name('custom.payment-full-success');

    Route::get('/custom-product', [ProductCustomController::class, 'index'])->name('custom.index');
    Route::get('/custom-product/my-requests', [ProductCustomController::class, 'myRequests'])->name('custom.my-requests');

    // Create dan view detail permintaan
    Route::post('/custom-product', [ProductCustomController::class, 'store'])->name('custom.store');
    Route::get('/custom-product/{id}', [ProductCustomController::class, 'show'])->name('custom.show');

    // Pengelolaan penawaran
    Route::post('/custom-product/{id}/accept-offer', [ProductCustomController::class, 'acceptOffer'])->name('custom.accept-offer');
    Route::post('/custom-product/{id}/reject-offer', [ProductCustomController::class, 'rejectOffer'])->name('custom.reject-offer');

    Route::get('/custom/retry-payment/{id}/{type}', [ProductCustomController::class, 'retryPayment'])
    ->name('custom.retry-payment')
    ->middleware('auth');
    // Pembayaran DP dan pelunasan
    Route::get('/custom-product/{id}/payment-dp', [ProductCustomController::class, 'paymentDp'])->name('custom.payment.dp');
    Route::get('/custom-product/{id}/payment-full', [ProductCustomController::class, 'paymentFull'])->name('custom.payment.full');

    // Informasi pengiriman
    Route::get('/custom-product/{id}/shipping', [ProductCustomController::class, 'shipping'])->name('custom.shipping');
    Route::post('/custom-product/{id}/shipping', [ProductCustomController::class, 'addShipping'])->name('custom.add-shipping');
    Route::get('/cities/{provinceId}', [ProductCustomController::class, 'getCities'])->name('cities.get');

    // Penyelesaian permintaan
    Route::post('/custom-product/{id}/complete', [ProductCustomController::class, 'markComplete'])->name('custom.mark-complete');

    // Tambah gambar referensi
    Route::post('/custom-product/{id}/add-reference', [ProductCustomController::class, 'addReference'])->name('custom.add-reference');

    Route::get('/delivery-manifest/{batchId}', [DeliveryManifestController::class, 'generateManifest'])
    ->name('delivery-manifest.generate');
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

    // Produk
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');

    // Keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/invoice/print/{order}', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoice.print');

    // Checkout & pengiriman
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/cities', [CheckoutController::class, 'getCities'])->name('checkout.cities');
    Route::get('/checkout/shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.shipping');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    // Tambahkan route ini di grup checkout
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'showPayment'])->name('checkout.payment');
        // Pembayaran
    Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
    Route::get('/payment/success/{order_id}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/pending/{order_id}', [PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/error/{order_id}', [PaymentController::class, 'error'])->name('payment.error');
    Route::get('/payment/retry/{order_id}', [PaymentController::class, 'retry'])->name('payment.retry');

    // Pesanan
    Route::get('/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/orders/{order}', [AccountController::class, 'orderDetail'])->name('account.orders.detail');
    Route::post('/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    Route::delete('/account/orders/{order}/delete', [AccountController::class, 'deleteOrder'])->name('account.orders.delete');
    Route::delete('/account/orders/delete-all', [AccountController::class, 'deleteAllOrders'])->name('account.orders.delete-all');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/delivery-manifest/{batchId}', [DeliveryManifestController::class, 'generateManifest'])
    ->name('delivery-manifest.generate');
/*
|--------------------------------------------------------------------------
| Admin Routes - Notifikasi
|--------------------------------------------------------------------------
*/
Route::get('/admin/notifications/{id}/mark-as-read', function ($id) {
    $notification = auth()->guard('admin')->user()->notifications()->findOrFail($id);
    $notification->update(['read_at' => now()]);

    return redirect()->back()->with('success', 'Notifikasi ditandai sebagai telah dibaca');
})->name('admin.notifications.mark-as-read');

Route::post('/custom-product/payment/notification', [ProductCustomController::class, 'handlePaymentNotification'])->name('custom.payment.notification');
/*
|--------------------------------------------------------------------------
| Auth Routes - Dari Laravel Breeze/Jetstream
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
