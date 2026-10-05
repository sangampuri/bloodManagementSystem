<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Stock left per blood group, expired batches excluded.
        // Result like ['A+' => 15, 'O-' => 4, ...]
        $stock = BloodInventory::notExpired()
            ->selectRaw('blood_group, SUM(units) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group');

        // Count my own requests by status in ONE query, not 5 separate ones.
        $requestCounts = $user->bloodRequests()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('user.dashboard', [
            'user'           => $user,
            'donor'          => $user->donor, // null if not registered as donor yet
            'stock'          => $stock,
            'requestCounts'  => $requestCounts,
            'recentRequests' => $user->bloodRequests()->latest()->take(5)->get(),
        ]);
    }
}