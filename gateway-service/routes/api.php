<?php

use App\Http\Controllers\Proxy\ProxyAuditController;
use App\Http\Controllers\Proxy\ProxyAuthController;
use App\Http\Controllers\Proxy\ProxyIpController;
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

Route::prefix('auth')->group(function () {
    Route::post('/register', [ProxyAuthController::class, 'register']);
    Route::post('/login', [ProxyAuthController::class, 'login']);
    Route::get('/me', [ProxyAuthController::class, 'me']);
    Route::post('/logout', [ProxyAuthController::class, 'logout']);
    Route::get('/users', [ProxyAuthController::class, 'users']);
});

Route::prefix('ip-addresses')->group(function () {
    Route::get('/', [ProxyIpController::class, 'index']);
    Route::post('/', [ProxyIpController::class, 'create']);
    Route::put('/{id}', [ProxyIpController::class, 'update']);
    Route::delete('/{id}', [ProxyIpController::class, 'destroy']);
});

Route::prefix('logs')->group(function () {
    Route::get('/', [ProxyAuditController::class, 'index']);
});