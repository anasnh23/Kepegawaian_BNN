<?php

namespace App\Models;

<<<<<<< HEAD
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
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'm_level'; // Mendefinisikan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'id_level'; // Mendefinisikan primary key dari tabel yang digunakan

    protected $fillable = ['level_name'];

    public function user(): HasMany
    {
        return $this->hasMany(UserModel::class, 'id_level', 'id_level'); // Perbaiki foreign key ke 'id_level'
    }
}
>>>>>>> 4d20b1604450ebb21361a61d0e27af0f9925b249
