<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ApiBkkController;
use App\Http\Controllers\Api\ApiBkmController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/klaim', [AdminController::class, 'klaim']);
Route::get('/send_data_kas', [AdminController::class, 'sendDataKas']);
Route::post('create-bkk', [ApiBkkController::class, 'store']);
Route::post('create-bkm', [ApiBkmController::class, 'store']);