<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\RiwayatPembayaran;
use App\Models\GajiBulanan;
use Illuminate\Support\Facades\Validator;

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
     *     path="/riwayat-pembayaran",
     *     summary="Ambil semua data riwayat pembayaran",
     *     description="Menampilkan seluruh daftar pembayaran",
     *     operationId="getAllPembayaran",
     *     tags={"Riwayat Pembayaran"},
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
     *     )
     * )
     */
    public function index() {
        try {
            $pembayaran = RiwayatPembayaran::all();
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/riwayat-pembayaran",
     *     summary="Tambah data pembayaran baru",
     *     description="Membuat record pembayaran baru",
     *     operationId="createPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_pembayaran", "waktu", "metode"},
     *             @OA\Property(property="id_pembayaran", type="string", example="PAY-001"),
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
     *             @OA\Property(property="errors", type="object", example={"id_pembayaran": {"The id pembayaran field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'id_pembayaran' => 'required|string|unique:riwayat_pembayaran,id_pembayaran',
                'waktu' => 'required|date',
                'metode' => 'required|string|in:Transfer Bank,Tunai,E-wallet',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $newPembayaran = RiwayatPembayaran::create($request->all());
            return response()->json($newPembayaran, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/riwayat-pembayaran/{id}",
     *     summary="Tampilkan detail pembayaran",
     *     description="Menampilkan data pembayaran berdasarkan ID",
     *     operationId="showPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="integer")
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
     *     )
     * )
     */
    public function show($id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/riwayat-pembayaran/{id}",
     *     summary="Update data pembayaran",
     *     description="Memperbarui data pembayaran berdasarkan ID",
     *     operationId="updatePembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
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
     *     )
     * )
     */
    public function update(Request $request, $id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'waktu' => 'date',
                'metode' => 'string|in:Transfer Bank,Tunai,E-wallet,QRIS',
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
     *     path="/riwayat-pembayaran/{id}",
     *     summary="Hapus data pembayaran",
     *     description="Menghapus data pembayaran berdasarkan ID",
     *     operationId="deletePembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menghapus data",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function destroy($id) {
        try {
            $deleted = RiwayatPembayaran::destroy($id);
            return response()->json(['success' => $deleted > 0]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/riwayat-pembayaran/{id}/gaji",
     *     summary="Ambil gaji bulanan berdasarkan ID pembayaran",
     *     description="Menampilkan data gaji bulanan yang terkait dengan riwayat pembayaran",
     *     operationId="getGajiByPembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari riwayat pembayaran",
     *         @OA\Schema(type="integer")
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
     *     )
     * )
     */
    public function getGajiBulanan($id) {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            $gaji = GajiBulanan::where('id_pembayaran', $id)->get();
            return response()->json($gaji);
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
?>