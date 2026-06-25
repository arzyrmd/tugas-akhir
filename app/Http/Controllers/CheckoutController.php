<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Province;
use App\Models\City;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        // Calculate subtotal
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        // Default shipping cost (can be updated via AJAX later)
        $shipping = 0;
        $total = $subtotal + $shipping;

        // Get all provinces for dropdown
        $provinces = Province::orderBy('name')->get();

        return view('checkout.index', compact('cart', 'cartItems', 'subtotal', 'shipping', 'total', 'provinces'));
    }

    public function getCities(Request $request)
    {
        try {
            $provinceId = $request->province_id;

            // Log for debugging
            Log::info('Looking for cities in province ID: ' . $provinceId);

            // Check if province exists
            $province = Province::find($provinceId);
            if (!$province) {
                return response()->json([
                    'error' => 'Provinsi tidak ditemukan',
                    'cities' => []
                ], 404);
            }

            // Get cities
            $cities = City::where('province_id', $provinceId)
                ->orderBy('name')
                ->get();

            Log::info('Found ' . $cities->count() . ' cities');

            return response()->json([
                'cities' => $cities
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cities: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data kota',
                'message' => $e->getMessage(),
                'cities' => []
            ], 500);
        }
    }

    public function calculateShipping(Request $request)
    {
        $cityId = $request->city_id;
        $cart = $this->getOrCreateCart();

        // Get all cart items with products
        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        // Calculate total weight in grams
        $totalWeight = $cartItems->sum(function($item) {
            return $item->product->weight * $item->quantity;
        });

        // Calculate volume in cm³ (length x width x height)
        $totalVolume = $cartItems->sum(function($item) {
            return ($item->product->length ?? 1) * ($item->product->width ?? 1) * ($item->product->height ?? 1) * $item->quantity;
        });

        // Convert weight to kg (for calculation)
        $weightInKg = $totalWeight / 1000;

        // Get city base cost
        $city = City::find($cityId);
        $baseCost = $city ? $city->shipping_cost : 0;

        // Shipping cost formula: base cost + (weight in kg * 1000) + (volume / 1000)
        $shippingCost = $baseCost + (ceil($weightInKg) * 1000) + (ceil($totalVolume / 1000) * 100);

        // Calculate subtotal
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        $total = $subtotal + $shippingCost;

        return response()->json([
            'shipping_cost' => $shippingCost,
            'formatted_shipping' => 'Rp ' . number_format($shippingCost, 0, ',', '.'),
            'total' => $total,
            'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.')
        ]);
    }

    private function getOrCreateCart()
    {
        if (Auth::check()) {
            // Logged in user
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        } else {
            // Guest user with session ID
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }

        return $cart;
    }

   public function process(Request $request)
{
    // Validate checkout form
    $validatedData = $request->validate([
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'full_name' => 'nullable|max:255',
        'email' => 'required|email|max:255',
        'province_id' => 'required|exists:provinces,id',
        'city_id' => 'required|exists:cities,id',
        'address' => 'required',
        'postal_code' => 'required|max:10',
        'phone' => 'required|max:20',
        'notes' => 'nullable',
    ]);

    try {
        DB::beginTransaction();

        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        }

        // Calculate subtotal
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        // Calculate shipping cost
        $city = City::find($request->city_id);
        // Get total weight in grams
        $totalWeight = $cartItems->sum(function($item) {
            return $item->product->weight * $item->quantity;
        });

        // Calculate volume in cm³ (length x width x height)
        $totalVolume = $cartItems->sum(function($item) {
            return ($item->product->length ?? 1) * ($item->product->width ?? 1) * ($item->product->height ?? 1) * $item->quantity;
        });

        // Convert weight to kg (for calculation)
        $weightInKg = $totalWeight / 1000;

        // Get city base cost
        $baseCost = $city ? $city->shipping_cost : 0;

        // Shipping cost formula: base cost + (weight in kg * 1000) + (volume / 1000)
        $shippingCost = $baseCost + (ceil($weightInKg) * 1000) + (ceil($totalVolume / 1000) * 100);

        // Total overall
        $total = $subtotal + $shippingCost;

        // Create order data
        $order = new Order();
        $order->user_id = Auth::id();
        $order->first_name = $request->first_name;
        $order->last_name = $request->last_name;
        $order->full_name = $request->full_name ?? $request->first_name . ' ' . $request->last_name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->province_id = $request->province_id;
        $order->city_id = $request->city_id;
        $order->postal_code = $request->postal_code;
        $order->notes = $request->notes;
        $order->subtotal = $subtotal;
        $order->shipping_cost = $shippingCost;
        $order->total = $total;
        $order->status = 'MENUNGGU PEMBAYARAN';
        $order->order_created_at = now();
        $order->save();

        // Save order items
        foreach ($cartItems as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->product_id;
            $orderItem->quantity = $item->quantity;
            $orderItem->price = $item->product->price;
            $orderItem->total = $item->quantity * $item->product->price;
            $orderItem->save();
        }

        // Clear cart after checkout
        CartItem::where('cart_id', $cart->id)->delete();

        DB::commit();

        // REDIRECT ke halaman payment (PRG Pattern)
        return redirect()->route('checkout.payment', $order->id)
            ->with('success', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error during checkout process: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.')
            ->withInput();
    }
}

// Tambahkan method baru untuk menampilkan halaman payment
public function showPayment($orderId)
{
    $order = Order::with(['orderItems.product.category', 'province', 'city'])
        ->findOrFail($orderId);

    // Pastikan user hanya bisa akses order miliknya sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403, 'Unauthorized access to order.');
    }

    try {
        // Generate snap token jika belum ada atau sudah expired
        if (!$order->snap_token) {
            $snapToken = $this->midtransService->generateSnapToken($order);

            // Simpan snap token ke database (opsional)
            $order->update(['snap_token' => $snapToken]);
        } else {
            $snapToken = $order->snap_token;
        }

        return view('checkout.payment', compact('order', 'snapToken'));

    } catch (\Exception $e) {
        Log::error('Error getting snap token: ' . $e->getMessage());

        return redirect()->route('account.orders.detail', $order->id)
            ->with('warning', 'Ada masalah dengan gateway pembayaran. Silakan coba lagi nanti.');
    }
}
}
