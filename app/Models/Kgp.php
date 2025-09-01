<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kgp extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'kgp';
    protected $primaryKey = 'id_kgp';

    // Field yang boleh diisi
    protected $fillable = [
        'id_user',
        'tahun_kgp',
        'tmt',
        'status',          // Menunggu / Disetujui / Ditolak
        'catatan',         // Alasan penolakan / catatan pimpinan
        'disetujui_oleh',  // ID pimpinan
        'disetujui_at',    // Timestamp persetujuan
    ];

    // Jika tabel pakai created_at & updated_at
    public $timestamps = false; // ubah ke true kalau nanti tabel ditambah kolom created_at, updated_at

    /**
     * Relasi ke pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke user pimpinan (yang menyetujui)
     */
    public function disetujuiOleh()
    {
        return $this->belongsTo(MUser::class, 'disetujui_oleh', 'id_user');
    }
}
