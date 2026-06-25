<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Jika user sudah terverifikasi, redirect ke dashboard
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Get categories untuk home page
        $categories = Category::with(['products' => function($query) {
            $query->orderBy('price', 'asc')->limit(1);
        }])->get();

        // Return home page dengan modal verifikasi email
        return view('home', [
            'categories' => $categories,
            'show_verify_email_modal' => true,
            'verification_email_sent' => session('verification_email_sent', false)
        ]);
    }
}
