@php
    $isAdmin = auth()->user()?->isAdmin() ?? false;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen lg:flex">

    {{-- Dark layer behind sidebar on phones. Click closes it. --}}
    <div id="sidebar-backdrop" data-sidebar-close class="fixed inset-0 z-30 hidden bg-ink/40 lg:hidden"></div>

    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 flex w-60 -translate-x-full flex-col border-r border-line bg-white transition-transform
                  lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:translate-x-0">
        <div class="flex h-16 items-center justify-between border-b border-line px-5">
            <x-logo />
            <button type="button" data-sidebar-close class="text-muted lg:hidden" aria-label="Close menu">
                <x-lucide-x class="h-5 w-5" />
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm" aria-label="Main menu">
            @include('partials.sidebar-nav')
        </nav>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-white px-4 sm:px-6">
            <button type="button" data-sidebar-open class="text-muted lg:hidden" aria-label="Open menu">
                <x-lucide-menu class="h-5 w-5" />
            </button>
            <span class="text-sm text-muted">{{ $isAdmin ? 'Administration' : 'Member area' }}</span>

            <div class="ml-auto text-right leading-tight">
                <p class="text-sm font-medium">{{ auth()->user()?->name ?? 'Preview user' }}</p>
                <p class="text-xs text-muted">{{ $isAdmin ? 'Administrator' : 'Member' }}</p>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>

    <x-confirm-dialog />

</body>
</html>