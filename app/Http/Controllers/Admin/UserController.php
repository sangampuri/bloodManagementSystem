<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->withCount(['bloodRequests'])
            ->with('donor')
           ->when($request->filled('search'), fn ($q) =>
            $q->where(fn ($q) => $q
                ->where('name', 'like', \App\Support\Search::likeTerm($request->query('search')))
                ->orWhere('email', 'like', \App\Support\Search::likeTerm($request->query('search')))
            )
        )
            ->when($request->filled('role'), fn ($q) =>
                $q->where('role', $request->query('role'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:user,admin'],
        ], [
            'email.unique' => 'Another account already uses that email.',
        ]);

        // Stop an admin from demoting themselves and getting locked out mid-session.
        if ($user->id === Auth::id() && $data['role'] !== 'admin') {
            return back()->withInput()->with('error', 'You can\'t change your own role away from admin.');
        }

        $user->update($data);

        return redirect('/admin/users')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You can\'t delete your own account while logged in as it.');
        }

        $user->delete();

        return back()->with('success', 'User removed. Their donor profile, requests, and notifications were removed with them.');
    }
}