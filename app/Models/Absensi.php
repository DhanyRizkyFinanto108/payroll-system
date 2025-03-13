<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    public $timestamps = false;
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'absensi';
    
    /**
     * Primary key yang digunakan oleh tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_absensi';
    
    /**
     * Menentukan apakah primary key adalah auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_absensi',
        'id_karyawan',
        'waktu',
        'keterangan',
    ];
    
    /**
     * Relasi ke model Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
    
    /**
     * Relasi ke model GajiBulanan.
     */
    public function gajiBulanan()
    {
        return $this->hasOne(GajiBulanan::class, 'id_absensi', 'id_absensi');
    }
}
