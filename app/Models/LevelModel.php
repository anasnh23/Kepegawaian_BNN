<?php

namespace App\Models;

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