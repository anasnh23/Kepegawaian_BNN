<?php

// app/Models/Pangkat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    protected $table = 'pangkat';
    protected $primaryKey = 'id_pangkat';
    public $timestamps = false;

    protected $fillable = ['id_user', 'id_ref_pangkat', 'tmt'];

public function refPangkat()
{
    return $this->belongsTo(RefGolonganPangkat::class, 'id_ref_pangkat');
}



        public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user');
    }
}
