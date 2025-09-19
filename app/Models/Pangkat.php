<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    protected $table = 'pangkat';
    protected $primaryKey = 'id_pangkat';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_jabatan',
        'id_ref_pangkat',
        'golongan_pangkat',
    ];

    /**
     * Relasi ke referensi golongan pangkat
     * (tabel ref_golongan_pangkat → kolom gaji_pokok ada di sini)
     */
    public function refPangkat()
    {
        return $this->belongsTo(RefGolonganPangkat::class, 'id_ref_pangkat', 'id_ref_pangkat');
    }

    /**
     * Relasi ke user (m_user)
     */
    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke jabatan user
     */
    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'id_jabatan', 'id_jabatan');
    }
}
