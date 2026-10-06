@extends('layouts.dashboard')
@section('title', 'Edit user')

@section('content')

    <x-page-header :title="'Edit ' . $user->name" />

    <form method="POST" action="{{ url('/admin/users/' . $user->id) }}" class="max-w-md space-y-5 rounded-md border border-line bg-white p-6" novalidate>
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="label">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="input @error('name') input-error @enderror">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="input @error('email') input-error @enderror">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="role" class="label">Role</label>
            <select id="role" name="role" class="input @error('role') input-error @enderror"
                    @disabled($user->id === auth()->id())>
                <option value="user" @selected(old('role', $user->role) === 'user')>User</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
            </select>
            @if ($user->id === auth()->id())
                <p class="hint">You can't change your own role.</p>
                {{-- disabled fields don't submit, so resend the real value --}}
                <input type="hidden" name="role" value="{{ $user->role }}">
            @endif
            @error('role') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 border-t border-line pt-5">
            <button type="submit" class="btn btn-primary" data-loading-text="Saving...">Save changes</button>
            <a href="{{ url('/admin/users') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

@endsection