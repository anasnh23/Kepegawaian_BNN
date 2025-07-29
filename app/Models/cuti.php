<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MUser;
use App\Models\ApprovalPimpinan;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';
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

    /**
     * Relasi ke tabel m_user (pegawai yang mengajukan cuti)
     */
    public function pegawai()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke admin/pimpinan yang menyetujui cuti (dari cuti.approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(MUser::class, 'approved_by', 'id_user');
    }

    /**
     * Relasi ke tabel approval_pimpinan
     */
    public function approvalPimpinan()
    {
        return $this->hasOne(ApprovalPimpinan::class, 'id_cuti', 'id_cuti');
    }
}
