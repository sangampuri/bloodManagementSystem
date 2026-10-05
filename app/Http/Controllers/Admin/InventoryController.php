<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodInventory;
use App\Support\BloodGroup;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $batches = BloodInventory::query()
            ->when($request->filled('blood_group'), fn ($q) =>
                $q->where('blood_group', $request->query('blood_group'))
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->where('batch_code', 'like', '%' . $request->query('search') . '%')
            )
            ->when($request->query('expiry') === 'expired', fn ($q) =>
                $q->whereDate('expiry_date', '<', today())
            )
            ->when($request->query('expiry') === 'soon', fn ($q) =>
                $q->whereBetween('expiry_date', [today(), today()->addDays(7)])
            )
            ->orderBy('expiry_date')
            ->paginate(10)
            ->withQueryString();

        // Summary strip: available units per group, right now.
        $stock = BloodInventory::notExpired()
            ->selectRaw('blood_group, SUM(units) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group');

        return view('admin.inventory.index', compact('batches', 'stock'));
    }

    public function create(): View
    {
        return view('admin.inventory.form', ['batch' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['added_by'] = Auth::id();

        BloodInventory::create($data);

        return redirect('/admin/inventory')->with('success', 'Stock batch added.');
    }

    public function edit(BloodInventory $batch): View
    {
        return view('admin.inventory.form', compact('batch'));
    }

    public function update(Request $request, BloodInventory $batch): RedirectResponse
    {
        $batch->update($this->validated($request, $batch->id));

        return redirect('/admin/inventory')->with('success', 'Stock batch updated.');
    }

    public function destroy(BloodInventory $batch): RedirectResponse
    {
        $batch->delete();

        return redirect('/admin/inventory')->with('success', 'Stock batch removed.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'blood_group'  => ['required', 'in:' . implode(',', BloodGroup::ALL)],
            'batch_code'   => ['required', 'string', 'max:30', 'unique:blood_inventories,batch_code' . ($ignoreId ? ",$ignoreId" : '')],
            'units'        => ['required', 'integer', 'min:1', 'max:1000'],
            'collected_on' => ['required', 'date', 'before_or_equal:today'],
            'expiry_date'  => ['required', 'date', 'after_or_equal:collected_on'],
        ], [
            'blood_group.required'  => 'Pick a blood group.',
            'batch_code.required'   => 'Enter a batch code.',
            'batch_code.unique'     => 'That batch code is already used. Each batch needs a unique code.',
            'units.required'        => 'Enter the number of units.',
            'units.min'             => 'Units must be at least 1.',
            'collected_on.required' => 'Enter the collection date.',
            'collected_on.before_or_equal' => 'Collection date cannot be in the future.',
            'expiry_date.required'  => 'Enter the expiry date.',
            'expiry_date.after_or_equal' => 'Expiry date cannot be before the collection date.',
        ]);
    }
}