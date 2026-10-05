<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Enter your email address.',
            'password.required' => 'Enter your password.',
        ]);

        // Auth::attempt hashes nothing itself: it reads the stored bcrypt hash
        // and runs password_verify() under the hood. Prepared statement, no raw SQL.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'That email and password combination does not match our records.');
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->isAdmin() ? '/admin' : '/dashboard'
        )->with('success', 'Welcome back, ' . Auth::user()->name . '.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out.');
    }
}