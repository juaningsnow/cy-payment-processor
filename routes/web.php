<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DownloadMediaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceBatchController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XeroController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

// Route::get('/', function () {
//     return view('dashboard');
// })->name('dashboard')->middleware('auth');


Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::get('/xero', [XeroController::class, 'status'])->name('xero_status');
Route::get('/callback', [XeroController::class, 'callback'])->name('xero_callback');
Route::get('set-active-company/{userCompanyId}', [UserController::class, 'setActive'])->name('set_active_company');

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/create', [SupplierController::class, 'create'])->name('supplier_create');
    Route::get('/{id}', [SupplierController::class, 'show'])->name('supplier_show');
    Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier_edit');
});

Route::prefix('media')->group(function () {
    Route::get('/{id}', [DownloadMediaController::class, 'show'])->name('media_download');
});

Route::prefix('user-management')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users');
    Route::get('/create', [UserController::class, 'create'])->name('user_create');
    Route::get('/{id}', [UserController::class, 'show'])->name('user_show');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user_edit');
});

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('companies');
    Route::get('/create', [CompanyController::class, 'create'])->name('company_create');
    Route::get('/{id}', [CompanyController::class, 'show'])->name('company_show');
    Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('company_edit');
});

// Route::prefix('currencies')->group(function () {
//     Route::get('/', [CurrencyController::class, 'index'])->name('currencies');
//     Route::get('/create', [CurrencyController::class, 'create'])->name('currency_create');
//     Route::get('/{id}', [CurrencyController::class, 'show'])->name('currency_show');
//     Route::get('/{id}/edit', [CurrencyController::class, 'edit'])->name('currency_edit');
// });


Route::prefix('invoice-batches')->group(function () {
    Route::get('/', [InvoiceBatchController::class, 'index'])->name('invoice-batches');
    Route::get('/create', [InvoiceBatchController::class, 'create'])->name('invoice_create');
    Route::get('/{id}/generate', [InvoiceBatchController::class, 'downloadTextFile'])->name('invoice-batches_generate');
    Route::get('/{id}', [InvoiceBatchController::class, 'show'])->name('invoice-batches_show');
    Route::get('/{id}/edit', [InvoiceBatchController::class, 'edit'])->name('invoice-batches_edit');
});

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('invoices');
    // Route::get('/create', [InvoiceController::class, 'create'])->name('invoices_create');
    Route::get('/{id}', [InvoiceController::class, 'show'])->name('invoice_show');
    Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice_edit');
    Route::get('download-xero-attachment/{id}', [InvoiceController::class, 'downloadXeroAttachment'])->name('invoice_download-attachment');
});

Route::prefix('invoice-histories')->group(function () {
    Route::get('/', [InvoiceController::class, 'index2'])->name('invoice-histories');
});

// Route::prefix('summary')->group(function () {
//     Route::get('/', [SummaryController::class, 'create'])->name('summary-create');
//     Route::get('/excel/{dateFrom}/{dateTo}', [SummaryController::class, 'exportExcel']);
//     Route::get('/csv/{dateFrom}/{dateTo}', [SummaryController::class, 'exportCsv']);
// });
