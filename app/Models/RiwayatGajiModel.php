<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatGajiModel extends Model
{
    protected $table = 'riwayat_gaji';
    protected $primaryKey = 'id_riwayat_gaji';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tanggal_berlaku',
        'gaji_pokok',
        'keterangan',
    ];


    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }
}
