<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MUser extends Authenticatable
{
    protected $table = 'm_user';
    protected $primaryKey = 'id_user';

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
}