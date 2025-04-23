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
     *     path="/api/riwayat-pembayaran",
     *     summary="Get all riwayat pembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RiwayatPembayaran")
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
        try {
            $pembayaran = RiwayatPembayaran::all();
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/riwayat-pembayaran",
     *     summary="Create new riwayat pembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_pembayaran", type="string", example="PMB-001"),
     *             @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 10:00:00"),
     *             @OA\Property(property="metode", type="string", example="Transfer Bank")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RiwayatPembayaran")
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
     *     path="/api/riwayat-pembayaran/{id}",
     *     summary="Get riwayat pembayaran by ID",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of riwayat pembayaran",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/RiwayatPembayaran")
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
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/riwayat-pembayaran/{id}",
     *     summary="Update riwayat pembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of riwayat pembayaran",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 14:30:00"),
     *             @OA\Property(property="metode", type="string", example="Tunai")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RiwayatPembayaran")
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
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            // Validasi request untuk update
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
     *     path="/api/riwayat-pembayaran/{id}",
     *     summary="Delete riwayat pembayaran",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of riwayat pembayaran",
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
            $deleted = RiwayatPembayaran::destroy($id);
            return response()->json(['success' => $deleted > 0]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/api/riwayat-pembayaran/{id}/gaji-bulanan",
     *     summary="Get gaji bulanan by pembayaran ID",
     *     tags={"Riwayat Pembayaran"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of riwayat pembayaran",
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
     */
    public function getGajiBulanan($id)
    {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            // Ambil data Gaji_bulanan yang terkait dengan RiwayatPembayaran ini
            $gaji = GajiBulanan::where('id_pembayaran', $id)->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}