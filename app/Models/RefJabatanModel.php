<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanModel extends Model
{
    protected $table = 'ref_jabatan';
    protected $primaryKey = 'id_ref_jabatan';
    public $timestamps = false;

    protected $fillable = [
        'nama_jabatan', 
        'eselon', 
        'keterangan', 
    ];
}
