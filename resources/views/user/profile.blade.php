@extends('layouts.dashboard')
@section('title', 'My profile')

@section('content')
    <x-page-header title="My profile" />

    <form method="POST" action="{{ url('/profile') }}" class="max-w-md space-y-5 rounded-md border border-line bg-white p-6" novalidate>
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

        <div class="border-t border-line pt-5">
            <label for="password" class="label">New password <span class="text-muted font-normal">(leave blank to keep current)</span></label>
            <input id="password" name="password" type="password" class="input @error('password') input-error @enderror">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input">
        </div>

        <button type="submit" class="btn btn-primary" data-loading-text="Saving...">Save changes</button>
    </form>
@endsection