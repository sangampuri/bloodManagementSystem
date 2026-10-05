@extends('layouts.public')
@section('title', 'Log in')

@section('content')
<div class="mx-auto max-w-md px-6 py-16">

    <h1 class="text-xl font-semibold tracking-tight">Log in</h1>
    <p class="mt-1 text-sm text-muted">Members and administrators use the same form.</p>

    @if (session('error'))
        <div class="mt-6"><x-alert type="error">{{ session('error') }}</x-alert></div>
    @endif
    @if ($errors->any())
        <div class="mt-6"><x-alert type="error">Some fields need a second look. Fix the marked ones and try again.</x-alert></div>
    @endif

    <form method="POST" action="{{ url('/login') }}" class="mt-6 space-y-5" novalidate>
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autofocus
                   class="input @error('email') input-error @enderror">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password"
                   class="input @error('password') input-error @enderror">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="remember" class="rounded border-line text-brand-600 focus:ring-brand-600">
            Keep me logged in
        </label>

        <button type="submit" class="btn btn-primary w-full" data-loading-text="Logging in...">Log in</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Don't have an account?
        <a href="{{ url('/register') }}" class="font-medium text-brand-700 hover:underline">Register</a>
    </p>

    <p class="mt-4 rounded-md border border-line bg-stone-50 p-3 text-center text-xs text-muted">
        Demo admin: admin@example.com / Admin@12345 — change after testing.
    </p>
</div>
@endsection