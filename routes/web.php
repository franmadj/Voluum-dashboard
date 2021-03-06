<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountController;

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

Route::get('/', function () {
    return redirect()->route('accounts');
});



Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [Controller::class, 'dashboard'])->name('dashboard');
    
    
    Route::get('/networks', [Controller::class, 'networks'])->name('networks');
    Route::get('/traffic-source', [Controller::class, 'traffic_source'])->name('traffic_source');
    
    Route::get('/get-data/{id}/{type}', [Controller::class, 'get_data'])->name('get.data');
    
    
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts');
    Route::get('/accounts/{account}/edit', [AccountController::class, 'edit'])->name('edit.accounts');
    Route::post('/accounts', [AccountController::class, 'store'])->name('add.accounts');
    Route::put('/accounts/{account}', [AccountController::class, 'update'])->name('update.accounts');
    Route::delete('/accounts/{account}', [AccountController::class, 'delete'])->name('delete.accounts');
});

