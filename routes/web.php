<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;

// Ubah rute '/' menjadi memanggil DashboardController
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('archives', ArchiveController::class);
Route::resource('loans', LoanController::class);