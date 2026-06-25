<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = Category::with(['products' => function($query) {
            $query->orderBy('price', 'asc')->limit(1);
        }])->get();

        return view('home', [
            'categories' => $categories,
            'show_register_modal' => true
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // PERBAIKAN: Redirect ke halaman email verification dengan flag untuk menampilkan modal
        return redirect()->route('verification.notice')->with([
            'status' => 'Registration successful! Please verify your email address.',
            'show_verify_email_modal' => true,
            'verification_email_sent' => true
        ]);
    }
}
