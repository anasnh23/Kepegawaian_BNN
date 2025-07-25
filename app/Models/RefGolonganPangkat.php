<?php

// app/Models/RefGolonganPangkat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefGolonganPangkat extends Model
{
    protected $table = 'ref_golongan_pangkat';
    protected $primaryKey = 'id_ref_pangkat';
    public $timestamps = false;

    protected $fillable = ['golongan_pangkat', 'gaji_pokok', 'masa_kerja_min', 'masa_kerja_maks', 'keterangan'];
}

