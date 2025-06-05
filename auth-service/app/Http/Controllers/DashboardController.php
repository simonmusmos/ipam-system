<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Users
        $totalUsers = User::count();

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
            ],
        ]);
    }
}
