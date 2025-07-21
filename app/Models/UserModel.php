<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'm_user';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id_level', 'nip', 'email', 'nama', 'username', 'password', 'jenis_kelamin', 'agama', 'no_tlp', 'foto',
    ];

    protected $hidden = ['password'];

    // PERBAIKAN: Kembalikan ke primary key asli
    public function getAuthIdentifierName()
    {
        return 'id_user';  // Atau return $this->primaryKey;
    }

    // Ini tetap benar
    public function getAuthPassword()
    {
        return $this->password;
    }

    // Relasi
    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'id_level', 'id_level');
    }

    // Jika Anda ingin custom username untuk login, tidak perlu method ini.
    // Itu sudah ditangani di controller dengan Auth::attempt(['username' => ..., 'password' => ...])
}