<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menampilkan keranjang belanja
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        // Hitung subtotal dan total
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        // Untuk ongkir (bisa disesuaikan dengan logika bisnis)
        $shipping = 0;
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cart', 'cartItems', 'subtotal', 'shipping', 'total'));
    }

    // Menambahkan produk ke keranjang
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product_id = $request->product_id;
        $quantity = $request->quantity;

        // Mendapatkan atau membuat keranjang
        $cart = $this->getOrCreateCart();

        // Periksa apakah produk sudah ada di keranjang
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product_id)
            ->first();

        // Dapatkan informasi produk untuk pesan notifikasi
        $product = Product::findOrFail($product_id);

        if ($cartItem) {
            // Update jumlah jika produk sudah ada
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Tambahkan item baru ke keranjang
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }

        // Hitung total item di keranjang untuk badge/counter
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');

        // Jika request AJAX, kembalikan JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'type' => 'success',
                'message' => $product->name . ' berhasil ditambahkan ke keranjang!',
                'cart_count' => $totalItems
            ]);
        }

        // Fallback untuk non-AJAX (redirect biasa)
        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    // Update kuantitas item keranjang
public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    $cartItem = CartItem::with('product')->findOrFail($id);
    $cartItem->quantity = $request->quantity;
    $cartItem->save();

    // Hitung ulang total keranjang
    $cart = $this->getOrCreateCart();
    $cartItems = CartItem::with('product')
        ->where('cart_id', $cart->id)
        ->get();

    // Hitung subtotal untuk item ini
    $itemSubtotal = $cartItem->quantity * $cartItem->product->price;

    // Hitung subtotal dan total keranjang
    $subtotal = $cartItems->sum(function($item) {
        return $item->quantity * $item->product->price;
    });

    $shipping = 0;
    $total = $subtotal + $shipping;

    // Hitung total item di keranjang untuk badge/counter
    $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');

    // Jika request AJAX, kembalikan JSON
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'type' => 'success',
            'message' => 'Jumlah ' . $cartItem->product->name . ' berhasil diperbarui!',
            'item_subtotal' => $itemSubtotal,
            'subtotal' => $subtotal,
            'total' => $total,
            'cart_count' => $totalItems
        ]);
    }

    return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diperbarui!');
}

    // Hapus item dari keranjang
    public function remove(Request $request, $id)
    {
        $cartItem = CartItem::with('product')->findOrFail($id);
        $productName = $cartItem->product->name;
        $cartItem->delete();

        // Hitung ulang total keranjang
        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->get();

        // Hitung subtotal dan total
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });

        $shipping = 0;
        $total = $subtotal + $shipping;

        // Hitung total item di keranjang untuk badge/counter
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');
        $emptyCart = $totalItems === 0;

        // Jika request AJAX, kembalikan JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'type' => 'success',
                'message' => $productName . ' berhasil dihapus dari keranjang!',
                'subtotal' => $subtotal,
                'total' => $total,
                'cart_count' => $totalItems,
                'empty_cart' => $emptyCart
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang!');
    }

    // Metode bantu untuk mendapatkan/membuat keranjang
    private function getOrCreateCart()
    {
        if (Auth::check()) {
            // User yang login
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        } else {
            // User tamu dengan session ID
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }

        return $cart;
    }
}
