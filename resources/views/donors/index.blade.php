@extends('layouts.dashboard')
@section('title', 'Donor list')

@section('content')

    <x-page-header title="Find blood donors" text="Search by blood group, location, or availability." />

    {{-- Filter bar. GET form so filters live in the URL, shareable and bookmarkable. --}}
    <form method="GET" action="{{ url('/donors') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-md border border-line bg-white p-4">
        <div>
            <label for="blood_group" class="label">Blood group</label>
            <select id="blood_group" name="blood_group" class="input">
                <option value="">Any</option>
                @foreach (\App\Support\BloodGroup::ALL as $group)
                    <option value="{{ $group }}" @selected(request('blood_group') === $group)>{{ $group }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="location" class="label">Location</label>
            <input id="location" name="location" type="text" placeholder="e.g. Itahari"
                   value="{{ request('location') }}" class="input">
        </div>

        <label class="flex items-center gap-2 pb-2.5 text-sm">
            <input type="checkbox" name="available_only" value="1" @checked(request('available_only'))
                   class="rounded border-line text-brand-600 focus:ring-brand-600">
            Available only
        </label>

        <button type="submit" class="btn btn-primary">
            <x-lucide-search class="h-4 w-4" /> Search
        </button>

        @if (request()->anyFilled(['blood_group', 'location', 'available_only']))
            <a href="{{ url('/donors') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-md border border-line bg-white">
        @if ($donors->isEmpty())
            <x-empty-state icon="search" title="No donors match that search"
                text="Try a different blood group, or widen the location.">
                <x-slot:action>
                    <a href="{{ url('/donors') }}" class="btn btn-secondary btn-sm">Clear filters</a>
                </x-slot:action>
            </x-empty-state>
        @else
            @php
                // Build a "?sort=x&direction=y" link for a column header, flipping direction if already active.
                $sortLink = fn (string $column) => request()->fullUrlWithQuery([
                    'sort' => $column,
                    'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc',
                ]);
            @endphp
            <table class="data-table">
                <thead>
                    <tr>
                        <th><a href="{{ $sortLink('blood_group') }}" class="flex items-center gap-1 hover:text-ink">Group @if ($sort === 'blood_group') <x-lucide-chevron-down class="h-3 w-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @endif</a></th>
                        <th><a href="{{ $sortLink('name') }}" class="flex items-center gap-1 hover:text-ink">Name @if ($sort === 'name') <x-lucide-chevron-down class="h-3 w-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @endif</a></th>
                        <th><a href="{{ $sortLink('location') }}" class="flex items-center gap-1 hover:text-ink">Location @if ($sort === 'location') <x-lucide-chevron-down class="h-3 w-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @endif</a></th>
                        <th>Availability</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donors as $donor)
                        <tr>
                            <td><x-blood-tag :group="$donor->blood_group" /></td>
                            <td>{{ $donor->donor_name }}</td>
                            <td class="text-muted">{{ $donor->location }}</td>
                            <td>
                                @if ($donor->is_available)
                                    <span class="inline-flex items-center gap-1.5 text-sm text-green-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span> Available
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-sm text-muted">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-300"></span> Unavailable
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                             @auth
                            @if ($donor->user_id === auth()->id())
                                <span class="text-xs text-muted">That's you</span>
                            @else
                                <a href="{{ url('/requests/create?donor_id=' . $donor->id) }}" class="btn btn-secondary btn-sm">Request</a>
                            @endif
                        @else
                            <a href="{{ url('/login') }}" class="btn btn-secondary btn-sm">Log in to request</a>
                        @endauth
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($donors->hasPages())
        <div class="mt-4">{{ $donors->links() }}</div>
    @endif

@endsection