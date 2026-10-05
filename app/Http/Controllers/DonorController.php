<?php

namespace App\Http\Controllers;

use App\Support\BloodGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DonorController extends Controller
{
    public function edit(): View
    {
        return view('user.donor-form', [
            'donor' => Auth::user()->donor, // null on first visit
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'blood_group'        => ['required', 'in:' . implode(',', BloodGroup::ALL)],
            'gender'              => ['required', 'in:male,female,other'],
            'age'                 => ['required', 'integer', 'between:18,65'],
            'phone'               => ['required', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'location'            => ['required', 'string', 'max:100'],
            'last_donation_date'  => ['nullable', 'date', 'before_or_equal:today'],
            'is_available'        => ['nullable', 'boolean'],
        ], [
            'blood_group.required' => 'Pick your blood group.',
            'blood_group.in'       => 'Pick one of the listed blood groups.',
            'gender.required'      => 'Select a gender.',
            'age.required'         => 'Enter your age.',
            'age.between'          => 'Donor age must be between 18 and 65.',
            'phone.required'       => 'Enter a phone number.',
            'phone.regex'          => 'Enter a valid phone number (digits, spaces, + or - only).',
            'location.required'    => 'Enter your location.',
            'last_donation_date.before_or_equal' => 'Last donation date cannot be in the future.',
        ]);

        // Checkbox sends nothing when unchecked, so a missing key means false.
        $data['is_available'] = $request->boolean('is_available');

        $wasNew = Auth::user()->donor === null;

        // updateOrCreate: match by user_id. Row exists -> update it. Row missing -> insert it.
        // One method, both "become a donor" and "edit profile" flows.
        Auth::user()->donor()->updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return redirect('/dashboard')->with(
            'success',
            $wasNew ? 'You are now a registered donor. Thank you.' : 'Your donor profile has been updated.'
        );
    }
}