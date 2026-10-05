@extends('layouts.dashboard')
@section('title', 'My requests')

@section('content')

    <x-page-header title="My requests" text="Track the status of everything you've submitted.">
        <x-slot:actions>
            <a href="{{ url('/requests/create') }}" class="btn btn-primary"><x-lucide-clipboard-plus class="h-4 w-4" /> New request</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex flex-wrap gap-2 text-sm">
        @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled'] as $value => $label)
            <a href="{{ url('/my-requests') . ($value ? '?status=' . $value : '') }}"
               @class([
                   'rounded-md px-3 py-1.5',
                   'bg-brand-600 text-white' => request('status', '') === $value,
                   'border border-line text-muted hover:bg-stone-50' => request('status', '') !== $value,
               ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-md border border-line bg-white">
        @if ($requests->isEmpty())
            <x-empty-state icon="clipboard-list" title="No requests here"
                text="Once you submit a request, it will show up in this list." />
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Hospital</th>
                        <th>Urgency</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        <tr>
                            <td><x-blood-tag :group="$req->blood_group" /></td>
                            <td>
                                <p>{{ $req->hospital_name }}</p>
                                <p class="text-xs text-muted">{{ $req->quantity }} unit(s)</p>
                            </td>
                            <td class="capitalize text-muted">{{ $req->urgency }}</td>
                            <td class="text-muted">{{ $req->created_at->format('d M, Y') }}</td>
                            <td><x-status-badge :status="$req->status" /></td>
                            <td class="max-w-[16rem] text-xs text-muted">{{ $req->admin_note }}</td>
                            <td class="text-right">
                                @if ($req->status === 'pending')
                                    <form method="POST" action="{{ url('/requests/' . $req->id . '/cancel') }}"
                                          data-confirm="This cancels your pending request."
                                          data-confirm-title="Cancel this request?"
                                          data-confirm-button="Yes, cancel">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($requests->hasPages())
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif

@endsection