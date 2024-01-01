<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/health', [\App\Http\Controllers\GeneralController::class, 'health']);


// Order Resources
Route::post('/orders', [\App\Http\Controllers\v1\OrderController::class, 'create']);

// Stock Resources
Route::get('/stock', [\App\Http\Controllers\GeneralController::class, 'stock']);
