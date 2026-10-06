<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonorSearchController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        // Whitelist. Never let a query string pick an arbitrary column to sort by
        // (that would let someone probe column names, or break the query).
        $sortable = ['name' => 'users.name', 'blood_group' => 'donors.blood_group', 'location' => 'donors.location'];
        $sortColumn = $sortable[$sort] ?? $sortable['name'];
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        $donors = Donor::query()
            ->join('users', 'users.id', '=', 'donors.user_id')
            ->select('donors.*', 'users.name as donor_name')
            ->when($request->filled('blood_group'), fn ($q) =>
                $q->where('donors.blood_group', $request->query('blood_group'))
            )
            ->when($request->filled('location'), fn ($q) =>
    $q->where('donors.location', 'like', \App\Support\Search::likeTerm($request->query('location')))
)
            ->when($request->boolean('available_only'), fn ($q) =>
                $q->where('donors.is_available', true)
            )
            ->orderBy($sortColumn, $direction)
            ->paginate(10)
            ->withQueryString(); // keeps filters in the pagination links

        return view('donors.index', [
            'donors'    => $donors,
            'sort'      => $sort,
            'direction' => $direction,
        ]);
    }
}