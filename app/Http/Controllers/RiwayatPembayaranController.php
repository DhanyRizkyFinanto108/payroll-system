<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\RiwayatPembayaran;
use App\Models\GajiBulanan;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Riwayat Pembayaran",
 *     description="API endpoints for managing riwayat pembayaran"
 * )
 */
class RiwayatPembayaranController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran",
     *     summary="Ambil semua data riwayat pembayaran",
     *     description="Menampilkan seluruh daftar pembayaran",
     *     operationId="getAllPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menampilkan data",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RiwayatPembayaran")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function index() {
        try {
            $pembayaran = RiwayatPembayaran::with('karyawan')->get();
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/riwayat-pembayaran",
     *     summary="Tambah data pembayaran baru",
     *     description="Membuat record pembayaran baru",
     *     operationId="createPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_karyawan", "waktu", "metode"},
     *             @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 10:00:00"),
     *             @OA\Property(property="metode", type="string", enum={"Transfer Bank", "Tunai", "E-wallet"}, example="Transfer Bank")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil membuat data baru",
     *         @OA\JsonContent(ref="#/components/schemas/RiwayatPembayaran")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"id_karyawan": {"The id karyawan field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'id_karyawan' => 'required|string|exists:karyawans,id_karyawan',
                'waktu' => 'required|date',
                'metode' => 'required|string|in:Transfer Bank,Tunai,E-wallet',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Verify if karyawan exists before creating payment
            $karyawan = Karyawan::find($request->id_karyawan);
            if (!$karyawan) {
                return response()->json(['error' => 'Karyawan not found. Data karyawan must exist before creating payment record.'], 422);
            }

            $data = $request->all();
            $data['id_pembayaran'] = (string) Str::uuid();

            $newPembayaran = RiwayatPembayaran::create($data);
            return response()->json($newPembayaran, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran/{id}",
     *     summary="Tampilkan detail pembayaran",
     *     description="Menampilkan data pembayaran berdasarkan ID",
     *     operationId="showPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menampilkan detail",
     *         @OA\JsonContent(ref="#/components/schemas/RiwayatPembayaran")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function show($id) {
        try {
            $pembayaran = RiwayatPembayaran::with('karyawan')->findOrFail($id);
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/riwayat-pembayaran/{id}",
     *     summary="Update data pembayaran",
     *     description="Memperbarui data pembayaran berdasarkan ID",
     *     operationId="updatePembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 10:00:00"),
     *             @OA\Property(property="metode", type="string", enum={"Transfer Bank", "Tunai", "E-wallet", "QRIS"}, example="QRIS")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil memperbarui data",
     *         @OA\JsonContent(ref="#/components/schemas/RiwayatPembayaran")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"metode": {"The metode field must be one of: Transfer Bank, Tunai, E-wallet, QRIS."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function update(Request $request, $id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'id_karyawan' => 'sometimes|required|string|exists:karyawans,id_karyawan',
                'waktu' => 'sometimes|required|date',
                'metode' => 'sometimes|required|string|in:Transfer Bank,Tunai,E-wallet,QRIS',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $pembayaran->update($request->all());
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/riwayat-pembayaran/{id}",
     *     summary="Soft delete data pembayaran",
     *     description="Menghapus data pembayaran berdasarkan ID (soft delete)",
     *     operationId="deletePembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menghapus data",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function destroy($id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            $pembayaran->delete(); // Using soft delete
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran/karyawan/{id}",
     *     summary="Ambil data pembayaran berdasarkan ID karyawan",
     *     description="Menampilkan semua riwayat pembayaran untuk karyawan tertentu",
     *     operationId="getPembayaranByKaryawan",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID karyawan",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menampilkan data",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RiwayatPembayaran")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Karyawan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Karyawan not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function getByKaryawan($id) {
        try {
            $karyawan = Karyawan::findOrFail($id);
            $pembayaran = RiwayatPembayaran::where('id_karyawan', $id)->get();
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran/{id}/gaji",
     *     summary="Ambil gaji bulanan berdasarkan ID pembayaran",
     *     description="Menampilkan data gaji bulanan yang terkait dengan riwayat pembayaran",
     *     operationId="getGajiByPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menampilkan data gaji",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GajiBulanan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function getGajiBulanan($id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            $gaji = GajiBulanan::join('absensis', 'gaji_bulanans.id_absensi', '=', 'absensis.id_absensi')
                ->where('absensis.id_karyawan', $pembayaran->id_karyawan)
                ->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran/{id}/pdf",
     *     summary="Generate PDF riwayat pembayaran",
     *     description="Generate dan download PDF untuk riwayat pembayaran",
     *     operationId="generatePembayaranPdf",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF berhasil diunduh",
     *         @OA\MediaType(mediaType="application/pdf")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    

    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran/{id}/download-pdf",
     *     summary="Download PDF riwayat pembayaran",
     *     description="Download existing PDF file or generate new one for riwayat pembayaran",
     *     operationId="downloadExistingPembayaranPdf",
     *     tags={"Riwayat Pembayaran"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF berhasil diunduh",
     *         @OA\MediaType(mediaType="application/pdf")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
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
    public function downloadPDF($id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);

            if (!$pembayaran->file_path) {
                return $this->generatePDF($id);
            }

            $path = storage_path('app/public/' . $pembayaran->file_path);

            if (file_exists($path)) {
                return response()->download($path);
            }

            // If file doesn't exist, generate a new one
            return $this->generatePDF($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="RiwayatPembayaran",
 *     title="Riwayat Pembayaran",
 *     description="Model Riwayat Pembayaran",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID record", example=1),
 *     @OA\Property(property="id_pembayaran", type="string", description="ID pembayaran unik", example="PAY-001"),
 *     @OA\Property(property="waktu", type="string", format="date-time", description="Waktu pembayaran", example="2025-04-23 10:00:00"),
 *     @OA\Property(property="metode", type="string", description="Metode pembayaran", example="Transfer Bank"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Waktu pembuatan record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Waktu update terakhir")
 * )
 *
 * @OA\Schema(
 *     schema="GajiBulanan",
 *     title="Gaji Bulanan",
 *     description="Model Gaji Bulanan",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID record", example=1),
 *     @OA\Property(property="id_pembayaran", type="string", description="ID pembayaran yang terkait", example="PAY-001"),
 *     @OA\Property(property="jumlah", type="number", format="float", description="Jumlah gaji", example=5000000),
 *     @OA\Property(property="bulan", type="string", description="Bulan gaji", example="April 2025"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Waktu pembuatan record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Waktu update terakhir")
 * )
 */
