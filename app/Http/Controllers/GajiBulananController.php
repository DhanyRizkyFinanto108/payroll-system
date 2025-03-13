<?php

namespace App\Http\Controllers;

use App\Models\GajiBulanan;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\RiwayatPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GajiBulananController extends Controller
{
    public function index()
    {
        $gaji = GajiBulanan::all();
        return response()->json($gaji);
    }

    public function store(Request $request)
    {
        try {
            // Validasi request
            $gaji = new GajiBulanan();
            $validator = $gaji->validateGajiBulanan($request);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $newGaji = GajiBulanan::create($request->all());
            return response()->json($newGaji, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $gaji = GajiBulanan::findOrFail($id);
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $gaji = GajiBulanan::findOrFail($id);
            
            // Validasi request untuk update
            $validator = Validator::make($request->all(), [
                'id_absensi' => 'string|exists:absensi,id_absensi',
                'id_pembayaran' => 'string|exists:riwayat_pembayaran,id_pembayaran',
                'nominal' => 'integer|min:0',
                'tanggal' => 'date',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $gaji->update($request->all());
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = GajiBulanan::destroy($id);
            return response()->json(['success' => $deleted > 0]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get gaji by karyawan id
     * 
     * @param string $id_karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByKaryawan($id_karyawan)
    {
        try {
            $karyawan = Karyawan::findOrFail($id_karyawan);
            $absensiIds = Absensi::where('id_karyawan', $id_karyawan)->pluck('id_absensi');
            $gaji = GajiBulanan::whereIn('id_absensi', $absensiIds)->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get gaji by period (month and year)
     * 
     * @param int $tahun
     * @param int $bulan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPeriode($tahun, $bulan)
    {
        try {
            $startDate = "{$tahun}-{$bulan}-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $gaji = GajiBulanan::whereBetween('tanggal', [$startDate, $endDate])->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get gaji by absensi id
     * 
     * @param string $id_absensi
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByAbsensi($id_absensi)
    {
        try {
            $absensi = Absensi::findOrFail($id_absensi);
            $gaji = GajiBulanan::where('id_absensi', $id_absensi)->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get gaji by pembayaran id
     * 
     * @param string $id_pembayaran
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPembayaran($id_pembayaran)
    {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id_pembayaran);
            $gaji = GajiBulanan::where('id_pembayaran', $id_pembayaran)->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}