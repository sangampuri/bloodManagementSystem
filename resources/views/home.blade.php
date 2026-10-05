@extends('layouts.public')
@section('title', 'Home')

@section('content')

    {{-- Hero --}}
    <section class="border-b border-line">
        <div class="mx-auto grid max-w-6xl gap-8 px-6 py-16 lg:grid-cols-2 lg:items-center lg:py-24">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Blood Management System</h1>
                <p class="mt-4 max-w-lg text-muted">
                    One place to find donors, check blood availability, and submit or track blood requests —
                    built to cut paperwork and speed up emergency response.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ url('/donors') }}" class="btn btn-primary"><x-lucide-search class="h-4 w-4" /> Find blood</a>
                    <a href="{{ url('/register') }}" class="btn btn-secondary"><x-lucide-heart-pulse class="h-4 w-4" /> Become a donor</a>
                    @guest
                        <a href="{{ url('/login') }}" class="btn btn-secondary">Log in</a>
                    @endguest
                </div>
            </div>

            {{-- Blood group grid, doubles as the "one handmade detail" --}}
            <div class="grid grid-cols-4 gap-2">
                @foreach (\App\Support\BloodGroup::ALL as $group)
                    <x-blood-tag :group="$group" class="w-full justify-center text-sm" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <h2 class="text-lg font-semibold">About the system</h2>
            <p class="mt-2 max-w-2xl text-muted">
                Donor records, blood stock, and requests used to live on paper and scattered spreadsheets.
                This system centralizes all three, so staff can search donors instantly, track stock by
                blood group and expiry, and see every request's status without phone calls.
            </p>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <h2 class="text-lg font-semibold">How it works</h2>
            <ol class="mt-6 grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['user-plus', 'Register', 'Create an account as a user, and optionally register as a donor with your blood group and location.'],
                    ['search', 'Search or request', 'Search donors by blood group and location, or submit a request with hospital details and urgency.'],
                    ['check-circle-2', 'Get matched', 'An administrator reviews the request and approves, rejects, or marks it completed as things progress.'],
                ] as $i => [$icon, $title, $text])
                    <li>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full border border-brand-600 text-sm font-semibold text-brand-700">
                            {{ $i + 1 }}
                        </div>
                        <h3 class="mt-3 text-sm font-semibold">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-muted">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Why donate --}}
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <h2 class="text-lg font-semibold">Why donate blood?</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['One donation can help up to three patients, from surgeries to accident recovery.'],
                    ['Blood has a shelf life. Regular donors keep the stock genuinely usable, not just numerically full.'],
                    ['Emergencies don\'t wait. A ready donor list shortens the time between a request and a match.'],
                ] as [$text])
                    <p class="border-l-2 border-brand-600 pl-4 text-sm text-muted">{{ $text }}</p>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Emergency search --}}
    <section class="border-b border-line bg-stone-50">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Need blood urgently?</h2>
                    <p class="mt-1 text-sm text-muted">Search available donors by blood group and location, right now.</p>
                </div>
                <a href="{{ url('/donors') }}" class="btn btn-primary"><x-lucide-search class="h-4 w-4" /> Search donors</a>
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <h2 class="text-lg font-semibold">Contact</h2>
            <p class="mt-2 text-sm text-muted">
                For urgent, off-platform assistance, reach the blood bank office directly at
                <span class="font-medium text-ink">01-000000</span> or
                <span class="font-medium text-ink">contact@example.com</span>.
            </p>
        </div>
    </section>

@endsection