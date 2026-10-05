<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\UserNotification;
use App\Support\BloodGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BloodRequestController extends Controller
{
    public function create(Request $request): View
    {
        // Came from a donor's "Request" button on the search page (Phase 8) — preselect that donor.
        $donor = null;
        if ($request->filled('donor_id')) {
            $donor = Donor::with('user')->find($request->query('donor_id'));
        }

        return view('user.request-form', compact('donor'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'donor_id'         => ['nullable', 'exists:donors,id'],
            'blood_group'      => ['required', 'in:' . implode(',', BloodGroup::ALL)],
            'quantity'         => ['required', 'integer', 'min:1', 'max:20'],
            'hospital_name'    => ['required', 'string', 'max:150'],
            'hospital_address' => ['required', 'string', 'max:255'],
            'contact_number'   => ['required', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'urgency'          => ['required', 'in:normal,urgent,critical'],
            'reason'           => ['required', 'string', 'max:255'],
            'message'          => ['nullable', 'string', 'max:1000'],
        ], [
            'blood_group.required'      => 'Pick the blood group needed.',
            'quantity.required'         => 'Enter how many units you need.',
            'quantity.max'              => 'For more than 20 units, please contact the blood bank directly.',
            'hospital_name.required'    => 'Enter the hospital name.',
            'hospital_address.required' => 'Enter the hospital address.',
            'contact_number.required'   => 'Enter a contact number.',
            'contact_number.regex'      => 'Enter a valid phone number (digits, spaces, + or - only).',
            'urgency.required'          => 'Select an urgency level.',
            'reason.required'           => 'Briefly state the reason for this request.',
        ]);
        if (! empty($data['donor_id'])) {
    $donorUserId = \App\Models\Donor::where('id', $data['donor_id'])->value('user_id');
    if ($donorUserId === Auth::id()) {
        return back()->withInput()->with('error', 'You can\'t submit a blood request against your own donor profile.');
    }
}

        // Stop the same person flooding the queue with repeat requests for the
        // same blood group while an earlier one is still unresolved.
        $alreadyPending = Auth::user()->bloodRequests()
            ->where('blood_group', $data['blood_group'])
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->withInput()->with(
                'error',
                "You already have a pending request for {$data['blood_group']}. Wait for it to be reviewed before submitting another."
            );
        }

        $data['user_id'] = Auth::id();

        $bloodRequest = BloodRequest::create($data);
        if ($bloodRequest->donor_id) {
    $donorUserId = \App\Models\Donor::where('id', $bloodRequest->donor_id)->value('user_id');
    if ($donorUserId) {
        \App\Models\UserNotification::create([
            'user_id' => $donorUserId,
            'message' => 'Someone submitted a blood request naming you as a possible donor. The blood bank will follow up if needed.',
        ]);
    }
}

        UserNotification::create([
            'user_id' => Auth::id(),
            'message' => 'Your blood request has been submitted successfully.',
        ]);

        return redirect('/my-requests')->with('success', 'Your request has been submitted. You will be notified once it is reviewed.');
    }

    public function myRequests(Request $request): View
    {
        $requests = Auth::user()->bloodRequests()
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->query('status'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.my-requests', compact('requests'));
    }

    public function cancel(BloodRequest $bloodRequest): RedirectResponse
    {
        abort_unless($bloodRequest->user_id === Auth::id(), 403);

        if ($bloodRequest->status !== 'pending') {
            return back()->with('error', 'Only a pending request can be cancelled.');
        }

        $bloodRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Your request has been cancelled.');
    }
}