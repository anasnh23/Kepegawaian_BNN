<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatJabatanModel extends Model
{
    protected $table = 'riwayat_jabatan';
    protected $primaryKey = 'id_riwayat_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_jabatan',
        'tmt_mulai',
        'tmt_selesai',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }
}
