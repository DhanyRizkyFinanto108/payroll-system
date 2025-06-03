<?php

namespace App\Helpers;

use App\Models\Karyawan;
use App\Models\Absensi;

class IdGenerator
{
    public static function generateKaryawanId()
    {
        $lastKaryawan = Karyawan::orderBy('id_karyawan', 'desc')->first();
        
        if (!$lastKaryawan) {
            return 'KRY-001';
        }

        $lastNumber = (int) substr($lastKaryawan->id_karyawan, 4);
        $newNumber = $lastNumber + 1;
        return 'KRY-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function generateAbsensiId()
    {
        $lastAbsensi = Absensi::orderBy('id_absensi', 'desc')->first();
        
        if (!$lastAbsensi) {
            return 'ABS-001';
        }

        $lastNumber = (int) substr($lastAbsensi->id_absensi, 4);
        $newNumber = $lastNumber + 1;
        return 'ABS-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
