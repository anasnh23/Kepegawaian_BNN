<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    protected $table = 'm_level'; // Sesuai dengan nama tabel kamu
    protected $primaryKey = 'id_level';
    public $timestamps = false;

    protected $fillable = [
        'level_name',
    ];

    // Relasi ke user
    public function users()
    {
        return $this->hasMany(MUser::class, 'id_level', 'id_level');
    }
}
