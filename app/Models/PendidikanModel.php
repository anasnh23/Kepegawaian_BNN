<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanModel extends Model
{
    protected $table = 'pendidikan';
    protected $primaryKey = 'id_pendidikan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jenis_pendidikan',
        'tahun_kelulusan',
    ];

    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }
}
