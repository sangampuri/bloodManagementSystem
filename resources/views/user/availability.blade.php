@extends('layouts.dashboard')
@section('title', 'Blood availability')

@section('content')

    <x-page-header title="Blood availability" text="Current stock by group. Expired batches are not counted." />

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach (\App\Support\BloodGroup::ALL as $group)
            @php $units = $stock[$group] ?? 0; @endphp
            <div class="rounded-md border border-line bg-white p-5 text-center">
                <x-blood-tag :group="$group" />
                <p class="mt-3 text-2xl font-semibold tabular-nums {{ $units === 0 ? 'text-brand-700' : '' }}">{{ $units }}</p>
                <p class="text-xs text-muted">units available</p>
            </div>
        @endforeach
    </div>

@endsection