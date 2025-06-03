<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\GajiBulananController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\RiwayatPembayaranController;
use App\Http\Controllers\AuthController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Karyawan Routes
Route::group(['prefix' => 'karyawan'], function () {
    Route::get('/', [KaryawanController::class, 'index']);
    Route::post('/', [KaryawanController::class, 'store']);
    Route::get('/{id_karyawan}', [KaryawanController::class, 'show']);
    Route::put('/{id_karyawan}', [KaryawanController::class, 'update']);
    Route::delete('/{id_karyawan}', [KaryawanController::class, 'destroy']);
    
    // Karyawan relation routes
    Route::get('/{id}/absensi', [KaryawanController::class, 'getAbsensi']);
    Route::get('/{id}/gaji', [KaryawanController::class, 'getGaji']);
    Route::get('/{id}/pembayaran', [KaryawanController::class, 'getPembayaran']);
});

// Absensi Routes
Route::group(['prefix' => 'absensi'], function () {
    Route::get('/', [AbsensiController::class, 'index']);
    Route::post('/', [AbsensiController::class, 'store']);
    Route::get('/{id}', [AbsensiController::class, 'show']);
    Route::put('/{id}', [AbsensiController::class, 'update']);
    Route::delete('/{id}', [AbsensiController::class, 'destroy']);
    Route::get('/karyawan/{id}', [AbsensiController::class, 'getByKaryawan']);
    Route::get('/periode/{tanggalawal}/{tanggalakhir}', [AbsensiController::class, 'getByPeriode']);
});

// Gaji Bulanan Routes
Route::group(['prefix' => 'gaji-bulanan'], function () {
    Route::get('/', [GajiBulananController::class, 'index']);
    Route::post('/', [GajiBulananController::class, 'store']);
    Route::get('/{id}', [GajiBulananController::class, 'show']);
    Route::put('/{id}', [GajiBulananController::class, 'update']);
    Route::delete('/{id}', [GajiBulananController::class, 'destroy']);
    Route::get('/karyawan/{id}', [GajiBulananController::class, 'getByKaryawan']);
    Route::get('/periode/{tahun}/{bulan}', [GajiBulananController::class, 'getByPeriode']);
    Route::get('/absensi/{id}', [GajiBulananController::class, 'getByAbsensi']);
    Route::get('/pembayaran/{id_pembayaran}', [GajiBulananController::class, 'getByPembayaran']);
});

// Riwayat Pembayaran Routes
Route::group(['prefix' => 'riwayat-pembayaran'], function () {
    Route::get('/', [RiwayatPembayaranController::class, 'index']);
    Route::post('/', [RiwayatPembayaranController::class, 'store']);
    Route::get('/{id}', [RiwayatPembayaranController::class, 'show']);
    Route::put('/{id}', [RiwayatPembayaranController::class, 'update']);
    Route::delete('/{id}', [RiwayatPembayaranController::class, 'destroy']);
    Route::get('/karyawan/{id}', [RiwayatPembayaranController::class, 'getByKaryawan']);
});