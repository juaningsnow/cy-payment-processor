<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceBatchController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\SupplierController;
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

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/create', [SupplierController::class, 'create'])->name('supplier_create');
    Route::get('/{id}', [SupplierController::class, 'show'])->name('supplier_show');
    Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('supplier_edit');
});


Route::prefix('invoice-batches')->group(function () {
    Route::get('/', [InvoiceBatchController::class, 'index'])->name('invoice-batches');
    Route::get('/{id}/generate', [InvoiceBatchController::class, 'downloadTextFile'])->name('invoice-batches_generate');
    Route::get('/{id}', [InvoiceBatchController::class, 'show'])->name('invoice-batches_show');
    Route::get('/{id}/edit', [InvoiceBatchController::class, 'edit'])->name('invoice-batches_edit');
});

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('invoices');
    // Route::get('/create', [InvoiceController::class, 'create'])->name('invoices_create');
    Route::get('/{id}', [InvoiceController::class, 'show'])->name('invoice_show');
    Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice_edit');
});

// Route::prefix('summary')->group(function () {
//     Route::get('/', [SummaryController::class, 'create'])->name('summary-create');
//     Route::get('/excel/{dateFrom}/{dateTo}', [SummaryController::class, 'exportExcel']);
//     Route::get('/csv/{dateFrom}/{dateTo}', [SummaryController::class, 'exportCsv']);
// });
