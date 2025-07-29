<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MUser;
use App\Models\Cuti;

class ApprovalPimpinan extends Model
{
    use HasFactory;

    protected $table = 'approval_pimpinan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_cuti',
        'dokumen_path',
        'status',
        'approved_by',
    ];

    public $timestamps = true;

    /**
     * Relasi ke tabel cuti
     */
    public function cuti()
    {
        return $this->belongsTo(Cuti::class, 'id_cuti', 'id_cuti');
    }

    /**
     * Relasi ke user yang menyetujui
     */
    public function approver()
    {
        return $this->belongsTo(MUser::class, 'approved_by', 'id_user');
    }
}
