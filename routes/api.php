<?php

use App\Http\Controllers\BankApiController;
use App\Http\Controllers\InvoiceApiController;
use App\Http\Controllers\InvoiceBatchApiController;
use App\Http\Controllers\InvoiceBatchDetailApiController;
use App\Http\Controllers\PurposeApiController;
use App\Http\Controllers\SummaryApiController;
use App\Http\Controllers\SupplierApiController;
use BaseCode\Auth\Controllers\UserApiController;
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

Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);
    Route::get('/logged-in', [UserApiController::class, 'loggedIn']);
    Route::get('{id}', [UserApiController::class, 'show']);
    Route::patch('{id}', [UserApiController::class, 'update']);
});

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierApiController::class, 'index']);
    Route::get('{id}', [SupplierApiController::class, 'show']);
    Route::post('/', [SupplierApiController::class, 'store']);
    Route::patch('{id}', [SupplierApiController::class, 'update']);
    Route::delete('{id}', [SupplierApiController::class, 'destroy']);
});

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceApiController::class, 'index']);
    Route::get('{id}', [InvoiceApiController::class, 'show']);
    Route::post('/', [InvoiceApiController::class, 'storeMultipleInvoice']);
    Route::patch('{id}', [InvoiceApiController::class, 'update']);
    Route::delete('{id}', [InvoiceApiController::class, 'destroy']);
});

Route::prefix('banks')->group(function () {
    Route::get('/', [BankApiController::class, 'index']);
});

Route::prefix('purposes')->group(function () {
    Route::get('/', [PurposeApiController::class, 'index']);
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
