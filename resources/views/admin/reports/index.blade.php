@extends('layouts.dashboard')
@section('title', 'Reports')

@section('content')

    <x-page-header title="Reports" text="Snapshot of donors, stock, and request activity.">
        <x-slot:actions>
            <a href="{{ url('/admin/reports/export/requests') }}" class="btn btn-secondary">
                <x-lucide-download class="h-4 w-4" /> Export requests (CSV)
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-10 lg:grid-cols-2">

        {{-- Donors by blood group --}}
        <section>
            <h2 class="text-sm font-semibold">Donors by blood group</h2>
            <div class="mt-3 rounded-md border border-line bg-white p-4">
                @php $maxDonors = max($donorsByGroup->max() ?? 0, 1); @endphp
                <div class="space-y-2.5">
                    @foreach (\App\Support\BloodGroup::ALL as $group)
                        @php $count = $donorsByGroup[$group] ?? 0; @endphp
                        <div class="flex items-center gap-3">
                            <x-blood-tag :group="$group" class="w-14 shrink-0 text-xs" />
                            <div class="h-2 flex-1 rounded-sm bg-stone-100">
                                <div class="h-2 rounded-sm bg-brand-600" style="width: {{ ($count / $maxDonors) * 100 }}%"></div>
                            </div>
                            <span class="w-6 shrink-0 text-right text-sm tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Current stock --}}
        <section>
            <h2 class="text-sm font-semibold">Current stock (non-expired)</h2>
            <div class="mt-3 rounded-md border border-line bg-white p-4">
                @php $maxStock = max($stockByGroup->max() ?? 0, 1); @endphp
                <div class="space-y-2.5">
                    @foreach (\App\Support\BloodGroup::ALL as $group)
                        @php $units = $stockByGroup[$group] ?? 0; @endphp
                        <div class="flex items-center gap-3">
                            <x-blood-tag :group="$group" class="w-14 shrink-0 text-xs" />
                            <div class="h-2 flex-1 rounded-sm bg-stone-100">
                                <div class="h-2 rounded-sm bg-brand-600" style="width: {{ ($units / $maxStock) * 100 }}%"></div>
                            </div>
                            <span class="w-10 shrink-0 text-right text-sm tabular-nums">{{ $units }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Requests by status --}}
        <section>
            <h2 class="text-sm font-semibold">Requests by status</h2>
            <div class="mt-3 divide-y divide-line rounded-md border border-line bg-white">
                @foreach (['pending', 'approved', 'completed', 'rejected', 'cancelled'] as $status)
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <x-status-badge :status="$status" />
                        <span class="text-sm tabular-nums">{{ $requestsByStatus[$status] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Monthly trend --}}
        <section>
            <h2 class="text-sm font-semibold">Requests, last 6 months</h2>
            <div class="mt-3 rounded-md border border-line bg-white p-4">
                @php $maxMonthly = max($monthlyTrend->max(), 1); @endphp
                <div class="space-y-2.5">
                    @foreach ($monthlyTrend as $month => $count)
                        <div class="flex items-center gap-3">
                            <span class="w-16 shrink-0 text-xs text-muted">{{ $month }}</span>
                            <div class="h-2 flex-1 rounded-sm bg-stone-100">
                                <div class="h-2 rounded-sm bg-brand-600" style="width: {{ ($count / $maxMonthly) * 100 }}%"></div>
                            </div>
                            <span class="w-6 shrink-0 text-right text-sm tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    </div>

@endsection