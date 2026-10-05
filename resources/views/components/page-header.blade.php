@props(['title', 'text' => null])

<div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-line pb-5">
    <div>
        <h1 class="text-xl font-semibold tracking-tight">{{ $title }}</h1>
        @if ($text)
            <p class="mt-1 text-sm text-muted">{{ $text }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex gap-2">{{ $actions }}</div>
    @endisset
</div>