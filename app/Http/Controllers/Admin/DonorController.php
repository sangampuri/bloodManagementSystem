<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonorController extends Controller
{
    public function index(Request $request): View
    {
        $donors = Donor::query()
            ->with('user')
            ->when($request->filled('blood_group'), fn ($q) =>
                $q->where('blood_group', $request->query('blood_group'))
            )
            ->when($request->filled('location'), fn ($q) =>
                $q->where('location', 'like', \App\Support\Search::likeTerm($request->query('location')))
            )
            ->when($request->filled('availability'), fn ($q) =>
                $q->where('is_available', $request->query('availability') === 'available')
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.donors.index', compact('donors'));
    }

    public function edit(Donor $donor): View
    {
        return view('admin.donors.edit', compact('donor'));
    }

    public function update(Request $request, Donor $donor): RedirectResponse
    {
        $data = $request->validate([
            'blood_group'  => ['required', 'in:' . implode(',', BloodGroup::ALL)],
            'gender'       => ['required', 'in:male,female,other'],
            'age'          => ['required', 'integer', 'between:18,65'],
            'phone'        => ['required', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'location'     => ['required', 'string', 'max:100'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $donor->update($data);

        return redirect('/admin/donors')->with('success', 'Donor record updated.');
    }

    public function destroy(Donor $donor): RedirectResponse
    {
        $donor->delete();

        return back()->with('success', 'Donor record removed. The user account itself was not deleted.');
    }
}