@extends('layouts.dashboard')
@section('title', 'Admin dashboard')

@section('content')

    <x-page-header title="Dashboard" text="System overview at a glance." />

    {{-- Top-line stats --}}
    <section class="grid grid-cols-2 divide-x divide-line border-y border-line sm:grid-cols-4">
        @foreach ([
            ['Total users', $totalUsers],
            ['Total donors', $totalDonors],
            ['Pending requests', $requestCounts['pending'] ?? 0],
            ['Total blood units', $totalUnits],
        ] as [$label, $value])
            <div class="px-4 py-4 first:pl-0">
                <p class="text-xs text-muted">{{ $label }}</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <div class="mt-10 grid gap-10 lg:grid-cols-3">

        {{-- Requests by status --}}
        <section>
            <h2 class="text-sm font-semibold">Requests by status</h2>
            <div class="mt-3 divide-y divide-line rounded-md border border-line bg-white">
                @foreach (['pending', 'approved', 'completed', 'rejected', 'cancelled'] as $status)
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <x-status-badge :status="$status" />
                        <span class="text-sm tabular-nums">{{ $requestCounts[$status] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Stock by group --}}
        <section>
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold">Blood inventory</h2>
                <a href="{{ url('/admin/inventory') }}" class="text-sm text-brand-700 hover:underline">Manage</a>
            </div>
            <div class="mt-3 divide-y divide-line rounded-md border border-line bg-white">
                @foreach (\App\Support\BloodGroup::ALL as $group)
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <x-blood-tag :group="$group" />
                        <span class="text-sm tabular-nums {{ ($stock[$group] ?? 0) === 0 ? 'text-brand-700' : '' }}">
                            {{ $stock[$group] ?? 0 }} units
                        </span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Recent requests --}}
        <section>
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold">Recent requests</h2>
                <a href="{{ url('/admin/requests') }}" class="text-sm text-brand-700 hover:underline">View all</a>
            </div>
            <div class="mt-3 rounded-md border border-line bg-white">
                @if ($recentRequests->isEmpty())
                    <x-empty-state icon="clipboard-list" title="No requests yet" />
                @else
                    <div class="divide-y divide-line">
                        @foreach ($recentRequests as $req)
                            <div class="flex items-center gap-3 px-4 py-2.5">
                                <x-blood-tag :group="$req->blood_group" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm">{{ $req->requester->name }}</p>
                                    <p class="text-xs text-muted">{{ $req->created_at->diffForHumans() }}</p>
                                </div>
                                <x-status-badge :status="$req->status" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>

@endsection