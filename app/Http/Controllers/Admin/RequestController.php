<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = BloodRequest::query()
            ->with(['requester', 'donor.user'])
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->query('status'))
            )
            ->when($request->filled('blood_group'), fn ($q) =>
                $q->where('blood_group', $request->query('blood_group'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, BloodRequest $bloodRequest): RedirectResponse
    {
        $data = $request->validate([
            'status'     => ['required', 'in:approved,rejected,completed'],
            'admin_note' => ['nullable', 'string', 'max:255'],
        ], [
            'status.required' => 'Choose an action.',
        ]);

        // Only sensible transitions. Stops e.g. "completed" being applied to a rejected request.
        $allowed = [
            'pending'  => ['approved', 'rejected'],
            'approved' => ['completed', 'rejected'],
        ];

        if (! in_array($data['status'], $allowed[$bloodRequest->status] ?? [], true)) {
            return back()->with('error', "A {$bloodRequest->status} request cannot be moved to {$data['status']}.");
        }

        $bloodRequest->update([
            'status'      => $data['status'],
            'admin_note'  => $data['admin_note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $messages = [
            'approved'  => 'Your request has been approved.',
            'rejected'  => 'Your request has been rejected.' . (! empty($data['admin_note']) ? ' ' . $data['admin_note'] : ''),
            'completed' => 'Your blood request has been marked as completed.',
        ];

        UserNotification::create([
            'user_id' => $bloodRequest->user_id,
            'message' => $messages[$data['status']],
        ]);

        return back()->with('success', 'Request updated.');
    }
}