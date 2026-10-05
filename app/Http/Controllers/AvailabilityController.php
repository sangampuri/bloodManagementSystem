<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use Illuminate\Contracts\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $stock = BloodInventory::notExpired()
            ->selectRaw('blood_group, SUM(units) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group');

        return view('user.availability', compact('stock'));
    }
}