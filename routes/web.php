<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
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

Route::get('/videos', [HomeController::class, 'videos'])->name('videos');

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products');
    Route::get('/create', [ProductController::class, 'create'])->name('product_create');
    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('product_edit');
});
