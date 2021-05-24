<?php

use App\Http\Controllers\InvoiceBatchApiController;
use App\Http\Controllers\InvoiceBatchDetailApiController;
use App\Http\Controllers\SummaryApiController;
use App\Http\Controllers\SupplierApiController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::prefix('products')->group(function () {
//     Route::get('/', [ProductApiController::class, 'index']);
//     Route::get('{id}', [ProductApiController::class, 'show']);
//     Route::post('/', [ProductApiController::class, 'store']);
//     Route::delete('{id}', [ProductApiController::class, 'destroy']);
// });

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierApiController::class, 'index']);
    Route::get('{id}', [SupplierApiController::class, 'show']);
    Route::post('/', [SupplierApiController::class, 'store']);
    Route::patch('{id}', [SupplierApiController::class, 'update']);
    Route::delete('{id}', [SupplierApiController::class, 'destroy']);
});


Route::prefix('invoice-batches')->group(function () {
    Route::get('/', [InvoiceBatchApiController::class, 'index']);
    Route::get('{id}', [InvoiceBatchApiController::class, 'show']);
    Route::post('/', [InvoiceBatchApiController::class, 'store']);
    Route::patch('{id}', [InvoiceBatchApiController::class, 'update']);
    Route::delete('{id}', [InvoiceBatchApiController::class, 'destroy']);
});

Route::prefix('invoice-batch-details')->group(function () {
    Route::get('/', [InvoiceBatchDetailApiController::class, 'index']);
});
