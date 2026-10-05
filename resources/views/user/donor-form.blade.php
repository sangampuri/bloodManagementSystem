@extends('layouts.dashboard')
@section('title', $donor ? 'Edit donor profile' : 'Become a donor')

@section('content')

    <x-page-header :title="$donor ? 'Edit donor profile' : 'Become a donor'"
        :text="$donor ? 'Keep your details current so people can reach you.' : 'Takes under a minute. You can update or pause availability any time.'" />

    <form method="POST" action="{{ url('/donor') }}" class="max-w-xl space-y-5 rounded-md border border-line bg-white p-6" novalidate>
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="blood_group" class="label">Blood group</label>
                <select id="blood_group" name="blood_group" class="input @error('blood_group') input-error @enderror">
                    <option value="">Choose one</option>
                    @foreach (\App\Support\BloodGroup::ALL as $group)
                        <option value="{{ $group }}" @selected(old('blood_group', $donor?->blood_group) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                @error('blood_group') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="gender" class="label">Gender</label>
                <select id="gender" name="gender" class="input @error('gender') input-error @enderror">
                    <option value="">Choose one</option>
                    @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender', $donor?->gender) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gender') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="age" class="label">Age</label>
                <input id="age" name="age" type="number" min="18" max="65" value="{{ old('age', $donor?->age) }}"
                       class="input @error('age') input-error @enderror">
                <p class="hint">Must be between 18 and 65.</p>
                @error('age') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="label">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $donor?->phone) }}"
                       class="input @error('phone') input-error @enderror">
                @error('phone') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="location" class="label">Location</label>
            <input id="location" name="location" type="text" placeholder="e.g. Itahari"
                   value="{{ old('location', $donor?->location) }}"
                   class="input @error('location') input-error @enderror">
            @error('location') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="last_donation_date" class="label">Last donation date <span class="text-muted font-normal">(optional)</span></label>
            <input id="last_donation_date" name="last_donation_date" type="date"
                   max="{{ now()->toDateString() }}"
                   value="{{ old('last_donation_date', $donor?->last_donation_date?->toDateString()) }}"
                   class="input @error('last_donation_date') input-error @enderror">
            @error('last_donation_date') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_available" value="1"
                   @checked(old('is_available', $donor?->is_available ?? true))
                   class="rounded border-line text-brand-600 focus:ring-brand-600">
            I'm currently available to donate
        </label>

        <div class="flex items-center gap-3 border-t border-line pt-5">
            <button type="submit" class="btn btn-primary" data-loading-text="Saving...">
                {{ $donor ? 'Save changes' : 'Register as donor' }}
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

@endsection