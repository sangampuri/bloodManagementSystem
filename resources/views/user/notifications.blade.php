@extends('layouts.dashboard')
@section('title', 'Notifications')

@section('content')

    <x-page-header title="Notifications">
        <x-slot:actions>
            <form method="POST" action="{{ url('/notifications/mark-all-read') }}">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">Mark all as read</button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="divide-y divide-line rounded-md border border-line bg-white">
        @forelse ($notifications as $note)
            <div class="flex items-start gap-3 px-4 py-3 {{ $note->is_read ? '' : 'bg-brand-50/40' }}">
                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full {{ $note->is_read ? 'bg-stone-300' : 'bg-brand-600' }}"></span>
                <div>
                    <p class="text-sm">{{ $note->message }}</p>
                    <p class="mt-0.5 text-xs text-muted">{{ $note->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <x-empty-state icon="bell" title="No notifications yet" />
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif

@endsection