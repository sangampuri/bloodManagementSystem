@props(['icon' => 'droplet', 'title', 'text' => null])

<div class="px-6 py-12 text-center">
    <x-dynamic-component :component="'lucide-' . $icon" class="mx-auto h-6 w-6 text-stone-400" />
    <p class="mt-3 text-sm font-medium">{{ $title }}</p>
    @if ($text)
        <p class="mx-auto mt-1 max-w-sm text-sm text-muted">{{ $text }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>