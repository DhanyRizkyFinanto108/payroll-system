<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Karyawan",
 *     description="API Endpoints untuk manajemen data karyawan"
 * )
 */
class KaryawanController extends Controller
{
    private function generateKaryawanId()
    {
        $lastKaryawan = Karyawan::orderBy('id_karyawan', 'desc')->first();
        
        if (!$lastKaryawan) {
            return 'KRY-001';
        }

        $lastNumber = (int) substr($lastKaryawan->id_karyawan, 4);
        $newNumber = $lastNumber + 1;
        return 'KRY-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @OA\Get(
     *     path="/karyawan",
     *     summary="Mendapatkan daftar semua karyawan",
     *     tags={"Karyawan"},
     *     @OA\Response(
     *         response=200,
     *         description="Data karyawan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data karyawan berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                     @OA\Property(property="nama", type="string", example="John Doe"),
     *                     @OA\Property(property="jabatan", type="string", example="Staff IT"),
     *                     @OA\Property(property="gaji_pokok", type="number", example=5000000)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $karyawan = Karyawan::all();
        return response()->json([
            'status' => 200,
            'message' => "Data karyawan berhasil diambil.",
            'data' => $karyawan
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/karyawan",
     *     summary="Menambahkan karyawan baru",
     *     tags={"Karyawan"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_karyawan", type="string", example="K001"),
     *             @OA\Property(property="nama", type="string", example="John Doe"),
     *             @OA\Property(property="jabatan", type="string", example="Staff IT"),
     *             @OA\Property(property="gaji_pokok", type="number", example=5000000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Karyawan berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Data karyawan berhasil dibuat"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                 @OA\Property(property="nama", type="string", example="John Doe"),
     *                 @OA\Property(property="jabatan", type="string", example="Staff IT"),
     *                 @OA\Property(property="gaji_pokok", type="number", example=5000000)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Gagal membuat data karyawan"),
     *             @OA\Property(property="error", type="string", example="The id karyawan field is required.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validate([
                'id_karyawan' => 'required|string|max:255|unique:karyawan,id_karyawan',
                'nama' => 'required|string',
                'jabatan' => 'required|string',
                'gaji_pokok' => 'required|numeric',
            ]);
                
            $karyawan = Karyawan::create($validatedData);
            
            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Data karyawan berhasil dibuat',
                'data' => $karyawan
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 422,
                'message' => 'Gagal membuat data karyawan',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/karyawan/{id_karyawan}",
     *     summary="Mendapatkan detail karyawan berdasarkan ID",
     *     tags={"Karyawan"},
     *     @OA\Parameter(
     *         name="id_karyawan",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail karyawan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data karyawan berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                 @OA\Property(property="nama", type="string", example="John Doe"),
     *                 @OA\Property(property="jabatan", type="string", example="Staff IT"),
     *                 @OA\Property(property="gaji_pokok", type="number", example=5000000)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show($id_karyawan)
    {
        $karyawan = Karyawan::find($id_karyawan);

        if(!$karyawan) {
            return response()->json([
                'status' => 404,
                'message' => 'Data karyawan tidak ditemukan.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data karyawan berhasil diambil.',
            'data' => $karyawan
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/karyawan/{id_karyawan}",
     *     summary="Memperbarui data karyawan",
     *     tags={"Karyawan"},
     *     @OA\Parameter(
     *         name="id_karyawan",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama", type="string", example="John Doe Updated"),
     *             @OA\Property(property="jabatan", type="string", example="Senior IT"),
     *             @OA\Property(property="gaji_pokok", type="number", example=6000000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data karyawan berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data karyawan berhasil diperbarui."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                 @OA\Property(property="nama", type="string", example="John Doe Updated"),
     *                 @OA\Property(property="jabatan", type="string", example="Senior IT"),
     *                 @OA\Property(property="gaji_pokok", type="number", example=6000000)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Gagal memperbarui data karyawan"),
     *             @OA\Property(property="error", type="string", example="The nama field must be a string.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id_karyawan)
    {
        DB::beginTransaction();
        
        try {
            $karyawan = Karyawan::find($id_karyawan);
            
            if (!$karyawan) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data karyawan tidak ditemukan',
                    'data' => null
                ], 404);
            }
            
            $validatedData = $request->validate([
                'nama' => 'sometimes|string|max:255',
                'jabatan' => 'sometimes|string|max:255',
                'gaji_pokok' => 'sometimes|numeric|min:0',
            ]);
            
            // Pastikan bahwa update benar-benar dijalankan
            $karyawan->fill($validatedData);
            $karyawan->save();
            
            // Verifikasi perubahan
            $karyawan = $karyawan->fresh();
            
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'message' => 'Data karyawan berhasil diperbarui.',
                'data' => $karyawan
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 422,
                'message' => 'Gagal memperbarui data karyawan',
                'error' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * @OA\Delete(
     *     path="/karyawan/{id_karyawan}",
     *     summary="Menghapus data karyawan",
     *     tags={"Karyawan"},
     *     @OA\Parameter(
     *         name="id_karyawan",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Karyawan berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data karyawan berhasil dihapus."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Gagal menghapus karyawan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="Gagal menghapus data karyawan"),
     *             @OA\Property(property="error", type="string", example="Gagal menghapus data karyawan")
     *         )
     *     )
     * )
     */
    public function destroy($id_karyawan)
    {
        DB::beginTransaction();
        
        try {
            $karyawan = Karyawan::find($id_karyawan);

            if(!$karyawan) {
                return response()->json([
                    'status' => 404,
                    'message' => "Data karyawan tidak ditemukan",
                    'data' => null
                ], 404);
            }
            
            // Pastikan hapus berjalan
            $karyawan->delete();
            
            // Verifikasi penghapusan
            $verifyDeleted = Karyawan::find($id_karyawan);
            if ($verifyDeleted) {
                throw new \Exception("Gagal menghapus data karyawan");
            }
            
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data karyawan berhasil dihapus.',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 500,
                'message' => 'Gagal menghapus data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/karyawan/{id}/absensi",
     *     summary="Mendapatkan data absensi karyawan berdasarkan ID",
     *     tags={"Karyawan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data absensi karyawan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data absensi karyawan berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                     @OA\Property(property="tanggal", type="string", format="date", example="2023-05-01"),
     *                     @OA\Property(property="status", type="string", example="Hadir"),
     *                     @OA\Property(property="keterangan", type="string", example="Masuk tepat waktu")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function getAbsensi($id)
    {
        // Implementation (akan diisi)
    }
    
    /**
     * @OA\Get(
     *     path="/karyawan/{id}/gaji",
     *     summary="Mendapatkan data gaji karyawan berdasarkan ID",
     *     tags={"Karyawan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data gaji karyawan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data gaji karyawan berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                     @OA\Property(property="bulan", type="integer", example=5),
     *                     @OA\Property(property="tahun", type="integer", example=2023),
     *                     @OA\Property(property="gaji_pokok", type="number", example=5000000),
     *                     @OA\Property(property="tunjangan", type="number", example=1000000),
     *                     @OA\Property(property="potongan", type="number", example=200000),
     *                     @OA\Property(property="total_gaji", type="number", example=5800000)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function getGaji($id)
    {
        // Implementation (akan diisi)
    }
    
    /**
     * @OA\Get(
     *     path="/karyawan/{id}/pembayaran",
     *     summary="Mendapatkan data riwayat pembayaran karyawan berdasarkan ID",
     *     tags={"Karyawan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data riwayat pembayaran karyawan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Data riwayat pembayaran karyawan berhasil diambil."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="id_karyawan", type="string", example="K001"),
     *                     @OA\Property(property="tanggal_pembayaran", type="string", format="date", example="2023-05-28"),
     *                     @OA\Property(property="jumlah", type="number", example=5800000),
     *                     @OA\Property(property="keterangan", type="string", example="Gaji Bulan Mei 2023"),
     *                     @OA\Property(property="metode_pembayaran", type="string", example="Transfer Bank")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function getPembayaran($id)
    {
        // Implementation (akan diisi)
    }
}