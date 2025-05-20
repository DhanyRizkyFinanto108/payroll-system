<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Sistem Manajemen Karyawan API",
 *     description="Dokumentasi API untuk sistem manajemen karyawan, absensi, dan penggajian",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="GajiBulanan",
 *     @OA\Property(property="id_gaji", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="id_absensi", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="nominal", type="integer", example=5000000),
 *     @OA\Property(property="tanggal", type="string", format="date", example="2025-04-23")
 * )
 *
 * @OA\Schema(
 *     schema="Absensi",
 *     @OA\Property(property="id_absensi", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 08:00:00"),
 *     @OA\Property(property="keterangan", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="Karyawan",
 *     @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="nama", type="string", example="John Doe"),
 *     @OA\Property(property="jabatan", type="string", example="Staff IT"),
 *     @OA\Property(property="gaji_pokok", type="number", example=5000000)
 * )
 *
 * @OA\Schema(
 *     schema="RiwayatPembayaran",
 *     @OA\Property(property="id_pembayaran", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="id_karyawan", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="waktu", type="string", format="date-time", example="2025-04-23 14:00:00"),
 *     @OA\Property(property="metode", type="string", example="Transfer Bank"),
 *     @OA\Property(property="file_path", type="string", nullable=true, example="pdfs/riwayat_pembayaran_550e8400-e29b-41d4-a716-446655440000.pdf")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
