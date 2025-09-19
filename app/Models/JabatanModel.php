<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanModel extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_ref_jabatan',
        'tmt',
    ];

    /**
     * Relasi ke referensi jabatan (master data)
     */
    public function refJabatan()
    {
        return $this->belongsTo(RefJabatanModel::class, 'id_ref_jabatan', 'id_ref_jabatan');
    }

    /**
     * Relasi ke user (m_user)
     */
    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }
}
