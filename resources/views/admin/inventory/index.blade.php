@extends('layouts.dashboard')
@section('title', 'Blood inventory')

@section('content')

    <x-page-header title="Blood inventory" text="Manage stock batches and monitor expiry.">
        <x-slot:actions>
            <a href="{{ url('/admin/inventory/create') }}" class="btn btn-primary">
                <x-lucide-plus class="h-4 w-4" /> Add stock
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Available units per group --}}
    <section class="mb-8 grid grid-cols-4 divide-x divide-line border-y border-line sm:grid-cols-8">
        @foreach (\App\Support\BloodGroup::ALL as $group)
            <div class="px-3 py-4 text-center first:pl-0">
                <x-blood-tag :group="$group" class="text-xs" />
                <p class="mt-2 text-lg font-semibold tabular-nums">{{ $stock[$group] ?? 0 }}</p>
            </div>
        @endforeach
    </section>

    <form method="GET" action="{{ url('/admin/inventory') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-md border border-line bg-white p-4">
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
            <label for="search" class="label">Batch code</label>
            <input id="search" name="search" type="text" placeholder="e.g. BMS-2609" value="{{ request('search') }}" class="input">
        </div>

        <div>
            <label for="expiry" class="label">Expiry</label>
            <select id="expiry" name="expiry" class="input">
                <option value="">All</option>
                <option value="soon" @selected(request('expiry') === 'soon')>Expiring within 7 days</option>
                <option value="expired" @selected(request('expiry') === 'expired')>Already expired</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><x-lucide-search class="h-4 w-4" /> Filter</button>
        @if (request()->anyFilled(['blood_group', 'search', 'expiry']))
            <a href="{{ url('/admin/inventory') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-md border border-line bg-white">
        @if ($batches->isEmpty())
            <x-empty-state icon="boxes" title="No stock batches match that filter"
                text="Adjust the filters, or add a new batch." />
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Batch code</th>
                        <th>Units</th>
                        <th>Collected</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $batch)
                        @php
                            $isExpired = $batch->expiry_date->isPast();
                            $isSoon = ! $isExpired && $batch->expiry_date->diffInDays(today()) <= 7;
                        @endphp
                        <tr>
                            <td><x-blood-tag :group="$batch->blood_group" /></td>
                            <td class="font-mono text-xs">{{ $batch->batch_code }}</td>
                            <td class="tabular-nums">{{ $batch->units }}</td>
                            <td class="text-muted">{{ $batch->collected_on->format('d M, Y') }}</td>
                            <td class="text-muted">{{ $batch->expiry_date->format('d M, Y') }}</td>
                            <td>
                                @if ($isExpired)
                                    <span class="text-xs font-medium text-brand-700">Expired</span>
                                @elseif ($isSoon)
                                    <span class="text-xs font-medium text-amber-700">Expiring soon</span>
                                @else
                                    <span class="text-xs text-muted">Good</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ url('/admin/inventory/' . $batch->id . '/edit') }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ url('/admin/inventory/' . $batch->id) }}"
                                          data-confirm="This permanently removes batch {{ $batch->batch_code }}."
                                          data-confirm-title="Remove this batch?"
                                          data-confirm-button="Yes, remove">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($batches->hasPages())
        <div class="mt-4">{{ $batches->links() }}</div>
    @endif

@endsection