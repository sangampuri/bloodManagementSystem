<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $requestCounts = BloodRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stock = BloodInventory::notExpired()
            ->selectRaw('blood_group, SUM(units) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group');

        return view('admin.dashboard', [
            'totalUsers'      => User::where('role', 'user')->count(),
            'totalDonors'     => Donor::count(),
            'totalRequests'   => BloodRequest::count(),
            'totalUnits'      => $stock->sum(),
            'requestCounts'   => $requestCounts,
            'stock'           => $stock,
            'recentRequests'  => BloodRequest::with('requester')->latest()->take(5)->get(),
        ]);
    }
}