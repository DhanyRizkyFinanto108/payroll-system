<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\GajiBulananController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\RiwayatPembayaranController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User authentication routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('auth.user');

    // Karyawan routes
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::post('/', [KaryawanController::class, 'store'])->name('store');
        Route::get('/{id_karyawan}', [KaryawanController::class, 'show'])->name('show');
        Route::put('/{id_karyawan}', [KaryawanController::class, 'update'])->name('update');
        Route::delete('/{id_karyawan}', [KaryawanController::class, 'destroy'])->name('destroy');
    });

    // Riwayat Pembayaran routes
    Route::prefix('riwayat-pembayaran')->name('riwayat-pembayaran.')->group(function () {
        // Standard CRUD operations
        Route::get('/', [RiwayatPembayaranController::class, 'index'])->name('index');
        Route::post('/', [RiwayatPembayaranController::class, 'store'])->name('store');
        Route::get('/{id}', [RiwayatPembayaranController::class, 'show'])->name('show');
        Route::put('/{id}', [RiwayatPembayaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [RiwayatPembayaranController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::get('/karyawan/{id}', [RiwayatPembayaranController::class, 'getByKaryawan'])->name('by-karyawan');
        Route::get('/{id}/pdf', [RiwayatPembayaranController::class, 'generatePDF'])->name('generate-pdf');
        Route::get('/{id}/download-pdf', [RiwayatPembayaranController::class, 'downloadPDF'])->name('download-pdf');
        Route::get('/{id}/gaji', [RiwayatPembayaranController::class, 'getGajiBulanan'])->name('gaji');
    });

    // Gaji Bulanan routes
    Route::prefix('gaji-bulanan')->name('gaji-bulanan.')->group(function () {
        // Standard CRUD operations
        Route::get('/', [GajiBulananController::class, 'index'])->name('index');
        Route::post('/', [GajiBulananController::class, 'store'])->name('store');
        Route::get('/{id}', [GajiBulananController::class, 'show'])->name('show');
        Route::put('/{id}', [GajiBulananController::class, 'update'])->name('update');
        Route::delete('/{id}', [GajiBulananController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::get('/karyawan/{id}', [GajiBulananController::class, 'getByKaryawan'])->name('by-karyawan');
        Route::get('/periode/{tahun}/{bulan}', [GajiBulananController::class, 'getByPeriode'])->name('by-periode');
        Route::get('/absensi/{id}', [GajiBulananController::class, 'getByAbsensi'])->name('by-absensi');
    });

    // Absensi routes
    Route::prefix('absensi')->name('absensi.')->group(function () {
        // Standard CRUD operations
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::post('/', [AbsensiController::class, 'store'])->name('store');
        Route::get('/{id}', [AbsensiController::class, 'show'])->name('show');
        Route::put('/{id}', [AbsensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AbsensiController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::get('/karyawan/{id}', [AbsensiController::class, 'getByKaryawan'])->name('by-karyawan');
        Route::get('/periode/{tanggalawal}/{tanggalakhir}', [AbsensiController::class, 'getByPeriode'])->name('by-periode');
    });
});
