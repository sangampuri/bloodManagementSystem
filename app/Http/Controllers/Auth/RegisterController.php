<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'     => 'Enter your full name.',
            'email.required'    => 'Enter your email address.',
            'email.email'       => 'That email address does not look right.',
            'email.unique'      => 'An account already uses this email. Try logging in instead.',
            'password.required' => 'Choose a password.',
            'password.min'      => 'Use at least 8 characters.',
            'password.confirmed'=> 'Password confirmation does not match.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            // role NOT set from $data on purpose. Column default is 'user'.
            // Even if someone adds role=admin to the request, it is ignored:
            // 'role' is not in User's $fillable list.
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard')->with('success', 'Welcome! Your account is ready.');
    }
}