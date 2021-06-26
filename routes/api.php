<?php

use App\Http\Controllers\BankApiController;
use App\Http\Controllers\CompanyApiController;
use App\Http\Controllers\InvoiceApiController;
use App\Http\Controllers\InvoiceBatchApiController;
use App\Http\Controllers\InvoiceBatchDetailApiController;
use App\Http\Controllers\PurposeApiController;
use App\Http\Controllers\SupplierApiController;
use App\Http\Controllers\UserApiController;
use BaseCode\Auth\Controllers\UserApiController as ControllersUserApiController;
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

Route::prefix('user-management')->group(function () {
    Route::get('/', [ControllersUserApiController::class, 'index']);
    Route::get('/logged-in', [ControllersUserApiController::class, 'loggedIn']);
    Route::get('{id}', [ControllersUserApiController::class, 'show']);
    Route::get('/detach-bank/{id}/{bankId}', [ControllersUserApiController::class, 'removeBank']);
    Route::get('/make-default/{id}/{bankId}', [ControllersUserApiController::class, 'makeDefault']);
    Route::post('/', [UserApiController::class, 'store']);
    Route::patch('{id}', [UserApiController::class, 'update']);
    Route::patch('/attach-bank/{id}', [ControllersUserApiController::class, 'addBank']);
    Route::delete('{id}', [UserApiController::class, 'destroy']);
});

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierApiController::class, 'index']);
    Route::get('{id}', [SupplierApiController::class, 'show']);
    Route::post('/', [SupplierApiController::class, 'store']);
    Route::patch('{id}', [SupplierApiController::class, 'update']);
    Route::delete('{id}', [SupplierApiController::class, 'destroy']);
});

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyApiController::class, 'index']);
    Route::get('{id}', [CompanyApiController::class, 'show']);
    Route::post('/', [CompanyApiController::class, 'store']);
    Route::patch('{id}', [CompanyApiController::class, 'update']);
    Route::delete('{id}', [CompanyApiController::class, 'destroy']);
});

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceApiController::class, 'index']);
    Route::get('{id}', [InvoiceApiController::class, 'show']);
    Route::post('/', [InvoiceApiController::class, 'storeMultipleInvoice']);
    Route::post('/pay', [InvoiceApiController::class, 'markAsPaid']);
    Route::post('{id}/attachment', [InvoiceApiController::class, 'addAttachment']);
    Route::post('destroy-multiple', [InvoiceApiController::class, 'destroyMultiple']);
    Route::patch('remove-attachment/{id}', [InvoiceApiController::class, 'removeAttachment']);
    Route::patch('{id}', [InvoiceApiController::class, 'update']);
    Route::delete('{id}', [InvoiceApiController::class, 'destroy']);
});

Route::prefix('banks')->group(function () {
    Route::get('/', [BankApiController::class, 'index']);
    Route::get('/user', [BankApiController::class, 'userIndex']);
});

Route::prefix('purposes')->group(function () {
    Route::get('/', [PurposeApiController::class, 'index']);
});


Route::prefix('invoice-batches')->group(function () {
    Route::get('/', [InvoiceBatchApiController::class, 'index']);
    Route::get('{id}', [InvoiceBatchApiController::class, 'show']);
    Route::post('/', [InvoiceBatchApiController::class, 'store']);
    Route::patch('{id}', [InvoiceBatchApiController::class, 'update']);
    Route::patch('add-invoices/{id}', [InvoiceBatchApiController::class, 'addInvoices']);
    Route::patch('cancel/{id}', [InvoiceBatchApiController::class, 'cancel']);
    Route::delete('{id}', [InvoiceBatchApiController::class, 'destroy']);
});

Route::prefix('invoice-batch-details')->group(function () {
    Route::get('/', [InvoiceBatchDetailApiController::class, 'index']);
});
