<?php

namespace App\Http\Controllers;

use App\Models\GajiBulanan;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\RiwayatPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="API Gaji Bulanan",
 *     version="1.0.0",
 *     description="API untuk manajemen gaji bulanan karyawan"
 * )
 */
class GajiBulananController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/gaji-bulanan",
     *     summary="Get all gaji bulanan",
     *     tags={"Gaji Bulanan"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GajiBulanan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function index()
    {
        $gaji = GajiBulanan::all();
        return response()->json($gaji);
    }

    /**
     * @OA\Post(
     *     path="/api/gaji-bulanan",
     *     summary="Create new gaji bulanan",
     *     tags={"Gaji Bulanan"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GajiBulanan")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GajiBulanan")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/gaji-bulanan/{id}",
     *     summary="Get gaji bulanan by ID",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of gaji bulanan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/GajiBulanan")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $gaji = GajiBulanan::findOrFail($id);
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/gaji-bulanan/{id}",
     *     summary="Update gaji bulanan",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of gaji bulanan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_absensi", type="string", example="ABS-001"),
     *             @OA\Property(property="id_pembayaran", type="string", example="PMB-001"),
     *             @OA\Property(property="nominal", type="integer", example=5000000),
     *             @OA\Property(property="tanggal", type="string", format="date", example="2025-04-23")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GajiBulanan")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/gaji-bulanan/{id}",
     *     summary="Delete gaji bulanan",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of gaji bulanan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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
     * @OA\Get(
     *     path="/api/gaji-bulanan/karyawan/{id_karyawan}",
     *     summary="Get gaji bulanan by karyawan ID",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="id_karyawan",
     *         in="path",
     *         required=true,
     *         description="ID of karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GajiBulanan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     * 
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
     * @OA\Get(
     *     path="/api/gaji-bulanan/periode/{tahun}/{bulan}",
     *     summary="Get gaji bulanan by period",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="tahun",
     *         in="path",
     *         required=true,
     *         description="Year",
     *         @OA\Schema(type="integer", example=2025)
     *     ),
     *     @OA\Parameter(
     *         name="bulan",
     *         in="path",
     *         required=true,
     *         description="Month (1-12)",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GajiBulanan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     * 
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
     * @OA\Get(
     *     path="/api/gaji-bulanan/absensi/{id_absensi}",
     *     summary="Get gaji bulanan by absensi ID",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID of absensi",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GajiBulanan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     * 
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
     * @OA\Get(
     *     path="/api/gaji-bulanan/pembayaran/{id_pembayaran}",
     *     summary="Get gaji bulanan by pembayaran ID",
     *     tags={"Gaji Bulanan"},
     *     @OA\Parameter(
     *         name="id_pembayaran",
     *         in="path",
     *         required=true,
     *         description="ID of pembayaran",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GajiBulanan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     * 
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