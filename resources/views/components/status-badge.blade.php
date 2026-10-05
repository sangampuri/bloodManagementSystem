@props(['status'])

@php
    $styles = [
        'pending'   => 'border-amber-200 bg-amber-50 text-amber-800',
        'approved'  => 'border-green-200 bg-green-50 text-green-800',
        'rejected'  => 'border-brand-100 bg-brand-50 text-brand-700',
        'completed' => 'border-sky-200 bg-sky-50 text-sky-800',
        'cancelled' => 'border-stone-200 bg-stone-100 text-stone-600',
    ];
@endphp

<span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-xs font-medium {{ $styles[$status] ?? $styles['cancelled'] }}">
    {{ ucfirst($status) }}
</span>