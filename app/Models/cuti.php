<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MUser;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti'; // nama tabel di database

    protected $primaryKey = 'id_cuti';

    protected $fillable = [
        'id_user',
        'jenis_cuti',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_cuti',
        'keterangan',
        'status',
        'approved_by',
    ];

    protected $dates = [
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'created_at',
        'updated_at'
    ];

    // Relasi ke tabel m_user (pegawai yang mengajukan cuti)
// Cuti.php sekarang (salah):


public function pegawai()
{
    return $this->belongsTo(MUser::class, 'id_user', 'id_user');
}



    // Relasi ke admin/pimpinan yang menyetujui cuti
    public function approver()
    {
        return $this->belongsTo(MUser::class, 'approved_by');
    }
}
