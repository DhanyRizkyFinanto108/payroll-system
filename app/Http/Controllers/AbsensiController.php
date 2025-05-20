<?php
namespace App\Http\Controllers;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     *     security={{"sanctum": {}}},
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
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
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Karyawan not found. Data karyawan must exist before creating an absensi record.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string|exists:karyawans,id_karyawan',
            'waktu' => 'required|date',
            'keterangan' => 'required|boolean',
        ]);

        // Verify that the karyawan exists before creating an absensi record
        $karyawan = Karyawan::find($request->id_karyawan);
        if (!$karyawan) {
            return response()->json([
                'status' => 422,
                'message' => 'Karyawan not found. Data karyawan must exist before creating an absensi record.',
            ], 422);
        }

        $data = $request->all();
        $data['id_absensi'] = (string) Str::uuid();

        $absensi = Absensi::create($data);
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
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID absensi",
     *         @OA\Schema(type="string", format="uuid")
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
     *             @OA\Property(property="message", type="string", example="Absensi not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
                'message' => 'Absensi not found.'
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
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID absensi",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
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
     *             @OA\Property(property="message", type="string", example="Absensi not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"id_karyawan": {"The selected id karyawan is invalid."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id_absensi)
    {
        $absensi = Absensi::find($id_absensi);
        if (!$absensi) {
            return response()->json([
                'status' => 404,
                'message' => 'Absensi not found.'
            ], 404);
        }

        $request->validate([
            'id_karyawan' => 'sometimes|required|string|exists:karyawans,id_karyawan',
            'waktu' => 'sometimes|required|date',
            'keterangan' => 'sometimes|required|boolean',
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
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id_absensi",
     *         in="path",
     *         required=true,
     *         description="ID absensi",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil dihapus",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Absensi not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy($id_absensi)
    {
        $absensi = Absensi::find($id_absensi);
        if (!$absensi) {
            return response()->json([
                'status' => 404,
                'message' => 'Absensi not found.'
            ], 404);
        }

        $absensi->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Absensi deleted successfully.'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/absensi/karyawan/{id_karyawan}",
     *     summary="Mendapatkan data absensi berdasarkan ID karyawan",
     *     tags={"Absensi"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id_karyawan",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi for karyawan retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Absensi")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Karyawan not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function getByKaryawan($id_karyawan)
    {
        $karyawan = Karyawan::find($id_karyawan);
        if (!$karyawan) {
            return response()->json([
                'status' => 404,
                'message' => 'Karyawan not found.'
            ], 404);
        }

        $absensi = Absensi::where('id_karyawan', $id_karyawan)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Absensi for karyawan retrieved successfully.',
            'data' => $absensi
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/absensi/periode/{tanggalawal}/{tanggalakhir}",
     *     summary="Mendapatkan data absensi berdasarkan periode tanggal",
     *     tags={"Absensi"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="tanggalawal",
     *         in="path",
     *         required=true,
     *         description="Tanggal awal periode (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="tanggalakhir",
     *         in="path",
     *         required=true,
     *         description="Tanggal akhir periode (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Absensi for period retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Absensi")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="tanggalawal", type="array", @OA\Items(type="string", example="The tanggalawal must be a valid date."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function getByPeriode($tanggalawal, $tanggalakhir)
    {
        // Validate date format
        $validator = validator(['tanggalawal' => $tanggalawal, 'tanggalakhir' => $tanggalakhir], [
            'tanggalawal' => 'required|date_format:Y-m-d',
            'tanggalakhir' => 'required|date_format:Y-m-d|after_or_equal:tanggalawal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $absensi = Absensi::whereBetween('waktu', [$tanggalawal, $tanggalakhir])->get();

        return response()->json([
            'status' => 200,
            'message' => 'Absensi for period retrieved successfully.',
            'data' => $absensi
        ], 200);
    }
}

//bagian absensi//