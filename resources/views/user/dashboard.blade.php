@extends('layouts.dashboard')
@section('title', 'Dashboard')

@section('content')

    <x-page-header :title="'Welcome, ' . explode(' ', $user->name)[0]" text="Here's what's happening with your account.">
        <x-slot:actions>
            @if (! $donor)
                <a href="{{ url('/donor') }}" class="btn btn-primary">
                    <x-lucide-heart-pulse class="h-4 w-4" /> Become a donor
                </a>
            @endif
            <a href="{{ url('/requests/create') }}" class="btn btn-secondary">
                <x-lucide-clipboard-plus class="h-4 w-4" /> New request
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Donor status strip --}}
    <section class="mb-10 rounded-md border border-line bg-white p-5">
        @if ($donor)
            <div class="flex flex-wrap items-center gap-4">
                <x-blood-tag :group="$donor->blood_group" class="text-sm" />
                <div>
                    <p class="text-sm font-medium">You're registered as a donor</p>
                    <p class="text-xs text-muted">
                        {{ $donor->is_available ? 'Marked available to donate' : 'Marked unavailable right now' }}
                        @if ($donor->last_donation_date)
                            · Last donation {{ $donor->last_donation_date->format('d M, Y') }}
                        @endif
                    </p>
                </div>
                <a href="{{ url('/donor') }}" class="btn btn-secondary btn-sm ml-auto">Edit donor profile</a>
            </div>
        @else
            <x-empty-state icon="heart-pulse" title="You're not a registered donor yet"
                text="Registering takes under a minute and helps people find you in an emergency.">
                <x-slot:action>
                    <a href="{{ url('/donor') }}" class="btn btn-primary btn-sm">Become a donor</a>
                </x-slot:action>
            </x-empty-state>
        @endif
    </section>

    {{-- My requests, by status --}}
    <section class="grid grid-cols-2 divide-x divide-line border-y border-line sm:grid-cols-4">
        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $key => $label)
            <div class="px-4 py-4 first:pl-0">
                <p class="text-xs text-muted">{{ $label }}</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums">{{ $requestCounts[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </section>

    <div class="mt-10 grid gap-10 lg:grid-cols-3">

        {{-- My recent requests --}}
        <section class="lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold">My recent requests</h2>
                <a href="{{ url('/my-requests') }}" class="text-sm text-brand-700 hover:underline">View all</a>
            </div>

            <div class="mt-3 overflow-x-auto rounded-md border border-line bg-white">
                @if ($recentRequests->isEmpty())
                    <x-empty-state icon="clipboard-list" title="No requests yet"
                        text="Submit a request when you or someone you know needs blood." />
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Hospital</th>
                                <th>Submitted</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentRequests as $request)
                                <tr>
                                    <td><x-blood-tag :group="$request->blood_group" /></td>
                                    <td class="text-muted">{{ $request->hospital_name }}</td>
                                    <td class="text-muted">{{ $request->created_at->format('d M, Y') }}</td>
                                    <td><x-status-badge :status="$request->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>

        {{-- Blood availability snapshot --}}
        <section>
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold">Blood availability</h2>
                <a href="{{ url('/availability') }}" class="text-sm text-brand-700 hover:underline">Full list</a>
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
    </div>

@endsection