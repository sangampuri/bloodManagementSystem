<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ], [
            'email.unique' => 'Another account already uses that email.',
        ]);

        $user->update($data);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:8', 'confirmed'],
            ], [
                'password.min' => 'New password must be at least 8 characters.',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profile updated.');
    }
}