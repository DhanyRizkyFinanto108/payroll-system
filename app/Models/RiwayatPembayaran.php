<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiwayatPembayaran extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'riwayat_pembayaran';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_pembayaran';
    
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pembayaran',
        'waktu',
        'metode',
    ];
    
    /**
     * Get the gaji bulanan records associated with the riwayat pembayaran.
     */
    public function gajiBulanans()
    {
        return $this->hasMany(GajiBulanan::class, 'id_pembayaran', 'id_pembayaran');
    }
}