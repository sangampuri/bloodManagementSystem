@extends('layouts.dashboard')
@section('title', 'Users')

@section('content')

    <x-page-header title="Users" text="View, edit, or remove accounts." />

    <form method="GET" action="{{ url('/admin/users') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-md border border-line bg-white p-4">
        <div>
            <label for="search" class="label">Name or email</label>
            <input id="search" name="search" type="text" value="{{ request('search') }}" class="input">
        </div>
        <div>
            <label for="role" class="label">Role</label>
            <select id="role" name="role" class="input">
                <option value="">Any</option>
                <option value="user" @selected(request('role') === 'user')>User</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><x-lucide-search class="h-4 w-4" /> Filter</button>
        @if (request()->anyFilled(['search', 'role']))
            <a href="{{ url('/admin/users') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-md border border-line bg-white">
        @if ($users->isEmpty())
            <x-empty-state icon="users" title="No users match that search" />
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Donor</th>
                        <th>Requests</th>
                        <th>Joined</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }} @if ($user->id === auth()->id()) <span class="text-xs text-muted">(you)</span> @endif</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span class="rounded-sm border px-2 py-0.5 text-xs font-medium {{ $user->role === 'admin' ? 'border-brand-200 bg-brand-50 text-brand-700' : 'border-line text-muted' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-muted">
                                @if ($user->donor)
                                    <x-blood-tag :group="$user->donor->blood_group" />
                                @else
                                    —
                                @endif
                            </td>
                            <td class="tabular-nums text-muted">{{ $user->blood_requests_count }}</td>
                            <td class="text-muted">{{ $user->created_at->format('d M, Y') }}</td>
                            <td class="text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ url('/admin/users/' . $user->id . '/edit') }}" class="btn btn-secondary btn-sm">Edit</a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ url('/admin/users/' . $user->id) }}"
                                              data-confirm="This removes {{ $user->name }}'s account, donor profile, requests, and notifications. This can't be undone."
                                              data-confirm-title="Delete this user?"
                                              data-confirm-button="Yes, delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif

@endsection