@extends('layouts.dashboard')
@section('title', $batch ? 'Edit batch' : 'Add stock')

@section('content')

    <x-page-header :title="$batch ? 'Edit batch ' . $batch->batch_code : 'Add stock batch'" />

    <form method="POST" action="{{ $batch ? url('/admin/inventory/' . $batch->id) : url('/admin/inventory') }}"
          class="max-w-xl space-y-5 rounded-md border border-line bg-white p-6" novalidate>
        @csrf
        @if ($batch) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="blood_group" class="label">Blood group</label>
                <select id="blood_group" name="blood_group" class="input @error('blood_group') input-error @enderror">
                    <option value="">Choose one</option>
                    @foreach (\App\Support\BloodGroup::ALL as $group)
                        <option value="{{ $group }}" @selected(old('blood_group', $batch?->blood_group) === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                @error('blood_group') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="batch_code" class="label">Batch code</label>
                <input id="batch_code" name="batch_code" type="text" placeholder="e.g. BMS-2610-001"
                       value="{{ old('batch_code', $batch?->batch_code) }}"
                       class="input @error('batch_code') input-error @enderror">
                @error('batch_code') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="units" class="label">Units</label>
            <input id="units" name="units" type="number" min="1" max="1000"
                   value="{{ old('units', $batch?->units) }}"
                   class="input @error('units') input-error @enderror">
            @error('units') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="collected_on" class="label">Collected on</label>
                <input id="collected_on" name="collected_on" type="date" max="{{ now()->toDateString() }}"
                       value="{{ old('collected_on', $batch?->collected_on?->toDateString()) }}"
                       class="input @error('collected_on') input-error @enderror">
                @error('collected_on') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="expiry_date" class="label">Expiry date</label>
                <input id="expiry_date" name="expiry_date" type="date"
                       value="{{ old('expiry_date', $batch?->expiry_date?->toDateString()) }}"
                       class="input @error('expiry_date') input-error @enderror">
                @error('expiry_date') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3 border-t border-line pt-5">
            <button type="submit" class="btn btn-primary" data-loading-text="Saving...">
                {{ $batch ? 'Save changes' : 'Add batch' }}
            </button>
            <a href="{{ url('/admin/inventory') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

@endsection