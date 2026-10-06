@extends('layouts.dashboard')
@section('title', 'Edit donor')

@section('content')

    <x-page-header :title="'Edit donor: ' . $donor->user->name" />

    <form method="POST" action="{{ url('/admin/donors/' . $donor->id) }}" class="max-w-xl space-y-5 rounded-md border border-line bg-white p-6" novalidate>
        @csrf
        @method('PUT')

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="blood_group" class="label">Blood group</label>
                <select id="blood_group" name="blood_group" class="input @error('blood_group') input-error @enderror">
                    @foreach (\App\Support\BloodGroup::ALL as $group)
                        <option value="{{ $group }}" @selected(old('blood_group', $donor->blood_group) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                @error('blood_group') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="gender" class="label">Gender</label>
                <select id="gender" name="gender" class="input">
                    @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender', $donor->gender) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="age" class="label">Age</label>
                <input id="age" name="age" type="number" min="18" max="65" value="{{ old('age', $donor->age) }}" class="input @error('age') input-error @enderror">
                @error('age') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone" class="label">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $donor->phone) }}" class="input @error('phone') input-error @enderror">
                @error('phone') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="location" class="label">Location</label>
            <input id="location" name="location" type="text" value="{{ old('location', $donor->location) }}" class="input @error('location') input-error @enderror">
            @error('location') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $donor->is_available)) class="rounded border-line text-brand-600 focus:ring-brand-600">
            Available to donate
        </label>

        <div class="flex items-center gap-3 border-t border-line pt-5">
            <button type="submit" class="btn btn-primary" data-loading-text="Saving...">Save changes</button>
            <a href="{{ url('/admin/donors') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

@endsection