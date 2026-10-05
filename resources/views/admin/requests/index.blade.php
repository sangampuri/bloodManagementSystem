@extends('layouts.dashboard')
@section('title', 'Blood requests')

@section('content')

    <x-page-header title="Blood requests" text="Review, approve, reject, or complete requests." />

    <form method="GET" action="{{ url('/admin/requests') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-md border border-line bg-white p-4">
        <div>
            <label for="status" class="label">Status</label>
            <select id="status" name="status" class="input">
                <option value="">Any</option>
                @foreach (['pending', 'approved', 'rejected', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="blood_group" class="label">Blood group</label>
            <select id="blood_group" name="blood_group" class="input">
                <option value="">Any</option>
                @foreach (\App\Support\BloodGroup::ALL as $group)
                    <option value="{{ $group }}" @selected(request('blood_group') === $group)>{{ $group }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><x-lucide-search class="h-4 w-4" /> Filter</button>
        @if (request()->anyFilled(['status', 'blood_group']))
            <a href="{{ url('/admin/requests') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-md border border-line bg-white">
        @if ($requests->isEmpty())
            <x-empty-state icon="clipboard-list" title="No requests match that filter" />
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Requested by</th>
                        <th>Hospital</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        <tr>
                            <td><x-blood-tag :group="$req->blood_group" /></td>
                            <td>
                                <p>{{ $req->requester->name }}</p>
                                <p class="text-xs text-muted">{{ $req->contact_number }}</p>
                            </td>
                            <td class="text-muted">{{ $req->hospital_name }}</td>
                            <td>
                                @if ($req->urgency === 'critical')
                                    <span class="text-xs font-medium text-brand-700">Critical</span>
                                @elseif ($req->urgency === 'urgent')
                                    <span class="text-xs font-medium text-amber-700">Urgent</span>
                                @else
                                    <span class="text-xs text-muted">Normal</span>
                                @endif
                            </td>
                            <td><x-status-badge :status="$req->status" /></td>
                            <td class="text-right">
                                @if (in_array($req->status, ['pending', 'approved']))
                                    <details class="inline-block text-left">
                                        <summary class="btn btn-secondary btn-sm cursor-pointer list-none">Review</summary>
                                        <form method="POST" action="{{ url('/admin/requests/' . $req->id . '/status') }}"
                                              class="mt-2 w-64 space-y-2 rounded-md border border-line bg-white p-3 shadow-sm">
                                            @csrf
                                            <textarea name="admin_note" rows="2" placeholder="Note (optional)" class="input text-xs"></textarea>
                                            <div class="flex flex-wrap gap-2">
                                                @if ($req->status === 'pending')
                                                    <button type="submit" name="status" value="approved" class="btn btn-primary btn-sm">Approve</button>
                                                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                                @else
                                                    <button type="submit" name="status" value="completed" class="btn btn-primary btn-sm">Mark completed</button>
                                                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                                @endif
                                            </div>
                                        </form>
                                    </details>
                                @else
                                    <span class="text-xs text-muted">
                                        Reviewed{{ $req->reviewed_at ? ' ' . $req->reviewed_at->format('d M') : '' }}
                                    </span>
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