@extends('layouts.public')
@section('title', 'Register')

@section('content')
<div class="mx-auto max-w-md px-6 py-16">

    <h1 class="text-xl font-semibold tracking-tight">Create your account</h1>
    <p class="mt-1 text-sm text-muted">Register to search donors and submit blood requests.</p>

    @if ($errors->any())
        <div class="mt-6"><x-alert type="error">Some fields need a second look. Fix the marked ones and try again.</x-alert></div>
    @endif

    <form method="POST" action="{{ url('/register') }}" class="mt-6 space-y-5" novalidate>
        @csrf

        <div>
            <label for="name" class="label">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autofocus
                   class="input @error('name') input-error @enderror">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}"
                   class="input @error('email') input-error @enderror">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password"
                   class="input @error('password') input-error @enderror">
            <p class="hint">At least 8 characters.</p>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input">
        </div>

        <button type="submit" class="btn btn-primary w-full" data-loading-text="Creating account...">Create account</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Already have an account?
        <a href="{{ url('/login') }}" class="font-medium text-brand-700 hover:underline">Log in</a>
    </p>
</div>
@endsection