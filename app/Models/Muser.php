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

    public function presensi()
{
    return $this->hasMany(PresensiModel::class, 'id_user', 'id_user');
}

// Cuti yang diajukan oleh pegawai
public function cuti()
{
    return $this->hasMany(Cuti::class, 'id_user');
}

// Cuti yang disetujui oleh user (jika user adalah admin/pimpinan)
public function approvedCuti()
{
    return $this->hasMany(Cuti::class, 'approved_by');
}

// Relasi ke JabatanModel
public function jabatan()
{
    return $this->hasOne(JabatanModel::class, 'id_user');
}

public function pangkat()
{
    return $this->hasOne(Pangkat::class, 'id_user');
}

public function kgp()
{
    return $this->hasOne(Kgp::class, 'id_user', 'id_user');
}


}
