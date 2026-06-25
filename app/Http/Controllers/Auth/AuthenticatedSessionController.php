<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Category; // TAMBAHKAN INI
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // PERBAIKAN: Ambil data categories
        $categories = Category::with(['products' => function($query) {
            $query->orderBy('price', 'asc')->limit(1);
        }])->get();

        // Atau jika ada relasi cheapestProduct:
        // $categories = Category::with('cheapestProduct')->get();

        return view('home', [
            'categories' => $categories,
            'show_login_modal' => true
        ]);
    }

    /**
     * Handle an incoming authentication request with rate limiting.
     */
    public function store(LoginRequest $request)
    {
        $key = 'login:' . $request->email . '|' . $request->ip();
        $maxAttempts = 3;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $retryAfter = ceil($seconds);

            // PERBAIKAN: Saat redirect dengan error, sediakan data categories
            $categories = Category::with(['products' => function($query) {
                $query->orderBy('price', 'asc')->limit(1);
            }])->get();

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam $retryAfter detik."
            ])->with([
                'categories' => $categories,
                'show_login_modal' => true,
                'retry_time' => $retryAfter
            ]);
        }

        try {
            $request->authenticate();
            RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()->intended('/')->with('success', 'Berhasil login!');
        } catch (ValidationException $e) {
            RateLimiter::hit($key, $decaySeconds);

            // PERBAIKAN: Saat redirect dengan error, sediakan data categories
            $categories = Category::with(['products' => function($query) {
                $query->orderBy('price', 'asc')->limit(1);
            }])->get();

            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->with([
                'categories' => $categories,
                'show_login_modal' => true
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Logout berhasil!');
    }
}
