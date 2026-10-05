@extends('layouts.dashboard')
@section('title', 'New blood request')

@section('content')

    <x-page-header title="Submit a blood request" text="Give as much detail as you can — it speeds up review." />

    @if ($donor)
        <div class="mb-6 flex items-center gap-3 rounded-md border border-line bg-stone-50 px-4 py-3 text-sm">
            <x-blood-tag :group="$donor->blood_group" />
            <span>Requesting in connection with donor <strong>{{ $donor->user->name }}</strong> in {{ $donor->location }}.</span>
        </div>
    @endif

    <form method="POST" action="{{ url('/requests') }}" class="max-w-xl space-y-5 rounded-md border border-line bg-white p-6" novalidate>
        @csrf
        @if ($donor)
            <input type="hidden" name="donor_id" value="{{ $donor->id }}">
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="blood_group" class="label">Blood group needed</label>
                <select id="blood_group" name="blood_group" class="input @error('blood_group') input-error @enderror">
                    <option value="">Choose one</option>
                    @foreach (\App\Support\BloodGroup::ALL as $group)
                        <option value="{{ $group }}" @selected(old('blood_group', $donor?->blood_group) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                @error('blood_group') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="quantity" class="label">Units needed</label>
                <input id="quantity" name="quantity" type="number" min="1" max="20" value="{{ old('quantity', 1) }}"
                       class="input @error('quantity') input-error @enderror">
                @error('quantity') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="hospital_name" class="label">Hospital name</label>
            <input id="hospital_name" name="hospital_name" type="text" value="{{ old('hospital_name') }}"
                   class="input @error('hospital_name') input-error @enderror">
            @error('hospital_name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="hospital_address" class="label">Hospital address</label>
            <input id="hospital_address" name="hospital_address" type="text" value="{{ old('hospital_address') }}"
                   class="input @error('hospital_address') input-error @enderror">
            @error('hospital_address') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="contact_number" class="label">Contact number</label>
                <input id="contact_number" name="contact_number" type="text" value="{{ old('contact_number') }}"
                       class="input @error('contact_number') input-error @enderror">
                @error('contact_number') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="urgency" class="label">Urgency</label>
                <select id="urgency" name="urgency" class="input @error('urgency') input-error @enderror">
                    @foreach (['normal' => 'Normal', 'urgent' => 'Urgent', 'critical' => 'Critical'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('urgency') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('urgency') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="reason" class="label">Reason</label>
            <input id="reason" name="reason" type="text" placeholder="e.g. Scheduled surgery, accident case"
                   value="{{ old('reason') }}" class="input @error('reason') input-error @enderror">
            @error('reason') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="message" class="label">Additional message <span class="text-muted font-normal">(optional)</span></label>
            <textarea id="message" name="message" rows="3" class="input @error('message') input-error @enderror">{{ old('message') }}</textarea>
            @error('message') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 border-t border-line pt-5">
            <button type="submit" class="btn btn-primary" data-loading-text="Submitting...">Submit request</button>
            <a href="{{ url('/dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

@endsection