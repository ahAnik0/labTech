<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/images', [ImageController::class, 'index']);
Route::post('/images', [ImageController::class, 'store'])->name('images.store');
Route::get('/images/{id}', [ImageController::class, 'show']);
Route::post('images/{image}', [ImageController::class, 'update'])->name('images.update');
Route::delete('/images/{id}', [ImageController::class, 'destroy']);
