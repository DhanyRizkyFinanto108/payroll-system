<?php
namespace App\Http\Controllers;
use App\Models\Absensi;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Absensi",
 *     description="API endpoints untuk mengelola absensi karyawan"
 * )
 */
class AbsensiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/absensi",
     *     summary="Mendapatkan semua data absensi",
     *     tags={"Absensi"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Absensi")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $absensi = Absensi::all();
        return response()->json([
        'status' => 200,
        'message' => "Absensi retrieved successfully.",
        'data' => $absensi
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/absensi",
     *     summary="Membuat data absensi baru",
     *     tags={"Absensi"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_absensi", type="string", example="ABS-001"),
     *             @OA\Property(property="id_karyawan", type="string", example="KRY-001"),
     *             @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 08:00:00"),
     *             @OA\Property(property="keterangan", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil dibuat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Absensi created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Absensi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function store(Request $request)
    {
       $request->validate([
        'id_absensi' => 'required|string|max:255',
        'id_karyawan' => 'required|string',
        'waktu' => 'required|date',
        'keterangan' => 'required|boolean',
       ]);
        
        $absensi = Absensi::create($request->all());
        return response()->json([
            'status' => 201,
            'message' => 'Absensi created successfully',
            'data' => $absensi
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/absensi/{id_absensi}",
     *     summary="Mendapatkan data absensi berdasarkan ID",
     *     tags={"Absensi"},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID absensi",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Absensi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Absensi not found."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show($id_absensi)
    {
       $absensi = Absensi::find($id_absensi);
       if(!$absensi) {
        return response()->json([
            'status' => 404,
            'message' => 'Absensi not found.',
            'data' => null
        ], 404);
       }
       return response()->json([
        'status' => 200,
        'message' => 'Absensi retrieved successfully.',
        'data' => $absensi
       ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/absensi/{id_absensi}",
     *     summary="Memperbarui data absensi",
     *     tags={"Absensi"},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID absensi",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_karyawan", type="string", example="KRY-002"),
     *             @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 09:30:00"),
     *             @OA\Property(property="keterangan", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil diperbarui",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Absensi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Absensi not found."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function update(Request $request, $id_absensi)
    {
        $absensi = Absensi::find($id_absensi);
        
        if (!$absensi) {
            return response()->json([
                'status' => 404,
                'message' => 'Absensi not found',
                'data' => null
            ], 404);
        }
        $request->validate([
            'id_karyawan' => 'string|max:255',
            'waktu' => 'date',
            'keterangan' => 'boolean',
        ]);
        $absensi->update($request->all());
        return response()->json([
            'status' => 200,
            'message' => 'Absensi updated successfully.',
            'data' => $absensi
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/absensi/{id_absensi}",
     *     summary="Menghapus data absensi",
     *     tags={"Absensi"},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID absensi",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil dihapus",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi deleted successfully."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Absensi not found."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function destroy($id_absensi)
    {
        $absensi = Absensi::find($id_absensi);
        if(!$absensi) {
            return response()->json([
                'status' => 404,
                'message' => "Absensi not found",
                'data' => null
            ], 404);
        }
        $absensi->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Absensi deleted successfully.',
            'data' => null
        ], 200);
    }
}