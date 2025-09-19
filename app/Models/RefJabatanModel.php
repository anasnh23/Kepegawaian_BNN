<?php

// app/Models/RefJabatanModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefJabatanModel extends Model
{
    protected $table = 'ref_jabatan';
    protected $primaryKey = 'id_ref_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'nama_jabatan',
        'eselon',
        'keterangan',
    ];

    /**
     * Relasi ke tabel jabatan
     */
    public function jabatan()
    {
        return $this->hasMany(JabatanModel::class, 'id_ref_jabatan', 'id_ref_jabatan');
    }
}
