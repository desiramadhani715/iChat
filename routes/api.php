<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StocklistController;
use App\Http\Controllers\TypeBlokController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/verify', 'AuthController@verify');
    Route::post('/verify-pin', 'AuthController@verify_pin');
    Route::post('/change-profile', 'AuthController@change_profile');
    Route::post('/logout', [AuthController::class, 'logout']);
});