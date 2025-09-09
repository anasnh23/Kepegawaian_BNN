<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    protected $table = 'pangkat';
    protected $primaryKey = 'id_pangkat';
    public $timestamps = false;

    protected $fillable = ['id_user', 'id_jabatan', 'id_ref_pangkat', 'golongan_pangkat'];

    public function refPangkat()
    {
        return $this->belongsTo(RefGolonganPangkat::class, 'id_ref_pangkat');
    }

    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user');
    }

    // Relasi ke JabatanModel (ini yang hilang atau salah sebelumnya)
    public function jabatanModel()
    {
        return $this->belongsTo(JabatanModel::class, 'id_jabatan'); // 'id_jabatan' adalah foreign key di tabel pangkat
    }
}
