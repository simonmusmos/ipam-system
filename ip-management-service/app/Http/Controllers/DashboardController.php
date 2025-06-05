<?php

namespace App\Http\Controllers;

use App\Models\IpAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total IPs
        $totalIPs = IpAddress::count();

        // IP trend (first month of the year to current month)
        $rawData = IpAddress::select(
            DB::raw("MONTH(created_at) as month"),
            DB::raw("COUNT(*) as count")
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('count', 'month');

        $months = [];
        for ($i = 1; $i <= now()->month; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            $months[$monthName] = $rawData->get($i, 0); // get count or 0
        }

        $trend = [
            'labels' => array_keys($months),
            'data' => array_values($months),
        ];

        // IP type breakdown (assumes you have a column like `type` = 'static'|'dynamic'|'reserved')
        $typeBreakdown = IpAddress::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');

        return response()->json([
            'stats' => [
                'total_ip_addresses' => $totalIPs,
            ],
            'charts' => [
                'ip_trend' => $trend,
                'ip_types' => $typeBreakdown,
            ]
        ]);
    }
}
