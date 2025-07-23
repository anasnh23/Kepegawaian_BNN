<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiModel extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = false;

    protected $fillable = [
        'id_user', 'tanggal',
        'jam_masuk', 'foto_masuk', 'lat_masuk', 'long_masuk',
        'jam_pulang', 'foto_pulang', 'lat_pulang', 'long_pulang',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }
}

