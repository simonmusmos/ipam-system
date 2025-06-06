<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['internal', 'jwt'])->group(function () {
    Route::get('/audit', [AuditLogController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
Route::post('/audit', [AuditLogController::class, 'store']);