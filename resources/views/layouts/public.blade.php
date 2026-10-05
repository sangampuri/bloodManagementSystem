<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">

    <header class="border-b border-line bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
            <x-logo />

            <nav class="hidden items-center gap-6 text-sm text-muted md:flex">
                <a href="{{ url('/#about') }}" class="hover:text-ink">About</a>
                <a href="{{ url('/#how-it-works') }}" class="hover:text-ink">How it works</a>
                <a href="{{ url('/donors') }}" class="hover:text-ink">Find blood</a>
                <a href="{{ url('/#contact') }}" class="hover:text-ink">Contact</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Go to dashboard</a>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-secondary btn-sm">Log in</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-line bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-6 py-6 text-sm text-muted">
            <x-logo />
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Built as a BCA academic project.</p>
        </div>
    </footer>

</body>
</html>