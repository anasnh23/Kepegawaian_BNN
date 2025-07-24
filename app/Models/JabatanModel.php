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
        'tahun_kelulusan',
        'tmt',
    ];

    public function jabatan()
    {
        return $this->belongsTo(RefJabatanModel::class, 'id_ref_jabatan');
    }
    public function user()
    {
        return $this->belongsTo(MuserModel::class, 'id_user');
    }
}
