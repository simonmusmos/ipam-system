<?php

use App\Http\Controllers\Proxy\ProxyAuthController;
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
});