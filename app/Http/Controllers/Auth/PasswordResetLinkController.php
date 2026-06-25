<?php

// PasswordResetLinkController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        $categories = Category::with(['products' => function($query) {
            $query->orderBy('price', 'asc')->limit(1);
        }])->get();

        return view('home', [
            'categories' => $categories,
            'show_forgot_password_modal' => true
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            // PERBAIKAN: Redirect ke route password.request dengan success message
            // dan flag untuk tetap membuka modal
            return redirect()->route('password.request')->with([
                'status' => __($status),
                'show_forgot_password_modal' => true,
                'forgot_password_success' => true
            ]);
        } else {
            // PERBAIKAN: Redirect ke route password.request dengan error
            // dan flag untuk tetap membuka modal
            return redirect()->route('password.request')
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)])
                ->with('show_forgot_password_modal', true);
        }
    }
}
