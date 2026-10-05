@props(['type' => 'success'])

@php
    $map = [
        'success' => ['border-green-300 bg-green-50 text-green-900', 'lucide-circle-check'],
        'error'   => ['border-brand-600/40 bg-brand-50 text-brand-700', 'lucide-circle-alert'],
        'info'    => ['border-line bg-white text-ink', 'lucide-info'],
    ];
    [$classes, $icon] = $map[$type] ?? $map['info'];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" class="flex items-start gap-3 rounded-md border px-4 py-3 text-sm {{ $classes }}">
    <x-dynamic-component :component="$icon" class="mt-0.5 h-4 w-4 shrink-0" />
    <div>{{ $slot }}</div>
</div>