<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kgp extends Model
{
    protected $table = 'kgp';
    protected $primaryKey = 'id_kgp';

    protected $fillable = [
        'id_user',
        'tahun_kgp',
        'tmt',
    ];

    public $timestamps = false;

    // Di model Kgp
public function pegawai()
{
    return $this->belongsTo(MUser::class, 'id_user', 'id_user');
}

}
