<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ActivityLogController;

// Rute Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Rute Pengguna yang Sudah Login & Disetujui (Admin maupun Karyawan)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/archives/{archive}/preview', [ArchiveController::class, 'preview'])->name('archives.preview');
    // Rute Pengajuan Akses Karyawan
    Route::post('/access-requests', [App\Http\Controllers\AccessRequestController::class, 'store'])->name('access-requests.store');
    // Rute Sirkulasi Pinjaman Fisik
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');

    // Rute Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/archives/create', [ArchiveController::class, 'create'])->name('archives.create');
        Route::post('/archives', [ArchiveController::class, 'store'])->name('archives.store');
        Route::get('/archives/{archive}/edit', [ArchiveController::class, 'edit'])->name('archives.edit');
        Route::put('/archives/{archive}', [ArchiveController::class, 'update'])->name('archives.update');
        Route::delete('/archives/{archive}', [ArchiveController::class, 'destroy'])->name('archives.destroy');
        // Rute Tong Sampah
        // Rute Arsip Terhapus (Pemulihan & Pemusnahan)
        Route::get('/archives/trash', [ArchiveController::class, 'trash'])->name('archives.trash');
        Route::post('/archives/{id}/restore', [ArchiveController::class, 'restore'])->name('archives.restore');
        Route::delete('/archives/trash/bulk-force-delete', [ArchiveController::class, 'bulkForceDelete'])->name('archives.bulk-force-delete');
        Route::delete('/archives/{id}/force-delete', [ArchiveController::class, 'forceDelete'])->name('archives.force-delete');
        // Rute Aksi Persetujuan Akses Dokumen
        Route::post('/access-requests/{id}/approve', [App\Http\Controllers\AccessRequestController::class, 'approve'])->name('access-requests.approve');
        Route::post('/access-requests/{id}/reject', [App\Http\Controllers\AccessRequestController::class, 'reject'])->name('access-requests.reject');

        Route::post('/loans/{id}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/loans/{id}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('/loans/{id}/return', [LoanController::class, 'markReturned'])->name('loans.return');
        Route::get('/archives-export', [App\Http\Controllers\ArchiveController::class, 'export'])->name('archives.export');
Route::post('/archives-import', [App\Http\Controllers\ArchiveController::class, 'import'])->name('archives.import');

        // Rute Persetujuan Akun
        Route::get('/approvals', [AuthController::class, 'approvals'])->name('approvals.index');
        Route::patch('/approvals/{user}/approve', [AuthController::class, 'approve'])->name('approvals.approve');
        Route::delete('/approvals/{user}/reject', [AuthController::class, 'reject'])->name('approvals.reject');

        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });
});