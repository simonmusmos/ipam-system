<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IpAddressController;

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

Route::middleware('jwt')->group(function () {
    Route::post('/ip-addresses', [IpAddressController::class, 'store']);
    Route::get('/ip-addresses', [IpAddressController::class, 'index']);
    Route::get('/ip-addresses/{ip}', [IpAddressController::class, 'getDetails']);
    Route::put('/ip-addresses/{ip}', [IpAddressController::class, 'update']);
    Route::delete('/ip-addresses/{ip}', [IpAddressController::class, 'destroy']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
});
