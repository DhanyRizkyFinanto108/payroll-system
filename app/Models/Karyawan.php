<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    public $timestamps = false;
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_karyawan',
        'nama',
        'jabatan',
        'gaji_pokok'
    ];

    // Definisikan event deleting untuk memastikan relasi juga dihapus
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($karyawan) {
            if (method_exists($karyawan, 'absensis')) {
                $karyawan->absensis()->delete();
            }
            if (method_exists($karyawan, 'riwayatPembayarans')) {
                $karyawan->riwayatPembayarans()->delete();
            }
        });
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_karyawan', 'id_karyawan');
    }

    public function riwayatPembayarans()
    {
        return $this->hasMany(RiwayatPembayaran::class, 'id_karyawan', 'id_karyawan');
    }
}
