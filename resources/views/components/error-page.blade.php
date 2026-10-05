@props(['code', 'title', 'text'])

<div class="mx-auto max-w-6xl px-6 py-24">
    <p class="text-sm text-muted">Error {{ $code }}</p>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ $title }}</h1>
    <p class="mt-2 max-w-md text-muted">{{ $text }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-6">Back to home</a>
</div>