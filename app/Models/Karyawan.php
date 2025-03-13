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
        'jabatan',
        'gaji_pokok',
        'nama'
    ];

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_karyawan', 'id_karyawan');
    }
}
