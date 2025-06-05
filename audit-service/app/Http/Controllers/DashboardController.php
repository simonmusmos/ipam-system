<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $recentActivities = AuditLog::whereDate('created_at', Carbon::today())->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weekCounts = AuditLog::selectRaw("DAYNAME(created_at) as day, COUNT(*) as count")
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->pluck('count', 'day');

        return response()->json([
            'stats' => [
                'activities_today' => $recentActivities,
            ],
            'charts' => [
                'week_breakdown' => $weekCounts,
            ]
        ]);
    }
}
