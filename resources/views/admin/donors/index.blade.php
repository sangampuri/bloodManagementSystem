@extends('layouts.dashboard')
@section('title', 'Donors')

@section('content')

    <x-page-header title="Donors" text="Everyone currently registered to donate." />

    <form method="GET" action="{{ url('/admin/donors') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-md border border-line bg-white p-4">
        <div>
            <label for="blood_group" class="label">Blood group</label>
            <select id="blood_group" name="blood_group" class="input">
                <option value="">Any</option>
                @foreach (\App\Support\BloodGroup::ALL as $group)
                    <option value="{{ $group }}" @selected(request('blood_group') === $group)>{{ $group }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="location" class="label">Location</label>
            <input id="location" name="location" type="text" value="{{ request('location') }}" class="input">
        </div>
        <div>
            <label for="availability" class="label">Availability</label>
            <select id="availability" name="availability" class="input">
                <option value="">Any</option>
                <option value="available" @selected(request('availability') === 'available')>Available</option>
                <option value="unavailable" @selected(request('availability') === 'unavailable')>Unavailable</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><x-lucide-search class="h-4 w-4" /> Filter</button>
        @if (request()->anyFilled(['blood_group', 'location', 'availability']))
            <a href="{{ url('/admin/donors') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-md border border-line bg-white">
        @if ($donors->isEmpty())
            <x-empty-state icon="heart-pulse" title="No donors match that filter" />
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Availability</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donors as $donor)
                        <tr>
                            <td><x-blood-tag :group="$donor->blood_group" /></td>
                            <td>{{ $donor->user->name }}</td>
                            <td class="text-muted">{{ $donor->phone }}</td>
                            <td class="text-muted">{{ $donor->location }}</td>
                            <td>
                                @if ($donor->is_available)
                                    <span class="text-sm text-green-700">Available</span>
                                @else
                                    <span class="text-sm text-muted">Unavailable</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ url('/admin/donors/' . $donor->id . '/edit') }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ url('/admin/donors/' . $donor->id) }}"
                                          data-confirm="This removes {{ $donor->user->name }}'s donor record. Their user account stays."
                                          data-confirm-title="Remove donor record?"
                                          data-confirm-button="Yes, remove">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($donors->hasPages())
        <div class="mt-4">{{ $donors->links() }}</div>
    @endif

@endsection