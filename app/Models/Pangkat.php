<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    protected $table = 'pangkat';
    protected $primaryKey = 'id_pangkat';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user');
    }

    public function refGolongan()
    {
        return $this->belongsTo(RefGolonganPangkat::class, 'id_ref_pangkat');
    }
}
