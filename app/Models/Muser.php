<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MUser extends Authenticatable
{
    protected $table = 'm_user';
    protected $primaryKey = 'id_user';

    public $timestamps = false; // 🚨 Ini penting untuk mencegah error!

    protected $fillable = [
        'id_level',
        'nip',
        'email',
        'nama',
        'username',
        'password',
        'jenis_kelamin',
        'agama',
        'no_tlp',
        'foto',
    ];

    protected $hidden = ['password'];

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'id_level');
    }

    public function pendidikan()
    {
        return $this->hasOne(PendidikanModel::class, 'id_user', 'id_user');
    }
}
