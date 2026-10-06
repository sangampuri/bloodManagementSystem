<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $donorsByGroup = Donor::query()
            ->selectRaw('blood_group, count(*) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group');

        $stockByGroup = BloodInventory::notExpired()
            ->selectRaw('blood_group, SUM(units) as total')
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group');

        $requestsByStatus = BloodRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Last 6 months, including months with zero requests (not just months that have rows).
        $monthly = BloodRequest::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $monthlyTrend = collect(range(5, 0))->mapWithKeys(function ($monthsAgo) use ($monthly) {
            $date = now()->subMonths($monthsAgo);
            $key = $date->format('Y-m');
            return [$date->format('M Y') => $monthly[$key] ?? 0];
        });

        return view('admin.reports.index', compact(
            'donorsByGroup', 'stockByGroup', 'requestsByStatus', 'monthlyTrend'
        ));
    }

    public function exportRequests(): StreamedResponse
    {
        $filename = 'blood-requests-' . now()->format('Y-m-d') . '.csv';

        $columns = ['ID', 'Requested by', 'Blood group', 'Quantity', 'Hospital', 'Urgency', 'Status', 'Submitted'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            BloodRequest::with('requester')->orderBy('id')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $req) {
                    fputcsv($handle, [
                        $req->id,
                        $req->requester->name,
                        $req->blood_group,
                        $req->quantity,
                        $req->hospital_name,
                        $req->urgency,
                        $req->status,
                        $req->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}