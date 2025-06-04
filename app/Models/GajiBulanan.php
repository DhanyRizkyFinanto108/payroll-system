<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class GajiBulanan extends Model
{
    // Jika tabel tidak memiliki kolom created_at dan updated_at
    public $timestamps = false;

    protected $table = 'gaji_bulanans';
    protected $primaryKey = 'id_gaji';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_gaji',
        'id_absensi',
        'nominal',
        'tanggal'
    ];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id_absensi');
    }

    // Relationship with RiwayatPembayaran has been removed as per requirements

    /**
     * Validate input data for GajiBulanan.
     *
     * @param \Illuminate\Http\Request|array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validateGajiBulanan($data)
    {
        // Jika $data adalah Request, konversi ke array
        $input = is_array($data) ? $data : $data->all();

        return Validator::make($input, [
            'id_gaji' => 'required|string|max:255|unique:gaji_bulanans,id_gaji',
            'id_absensi' => 'required|string|exists:absensis,id_absensi',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);
    }
}
