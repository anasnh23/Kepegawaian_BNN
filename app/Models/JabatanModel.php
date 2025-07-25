<?php

// app/Models/JabatanModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanModel extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = false;

    protected $fillable = ['id_user', 'id_ref_jabatan', 'tmt'];

public function refJabatan()
{
    return $this->belongsTo(RefJabatanModel::class, 'id_ref_jabatan');
}



    public function user()
    {
        return $this->belongsTo(Muser::class, 'id_user');
    }
}
