<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MUser;
use App\Models\ApprovalPimpinan;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';
    protected $primaryKey = 'id_cuti';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'jenis_cuti',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_cuti',
        'keterangan',
        'status',       // Menunggu | Disetujui | Ditolak
        'approved_by',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai'     => 'date',
        'tanggal_selesai'   => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /* ============
     *   RELASI
     * ============ */
    /** Pegawai yang mengajukan cuti */
    public function pegawai()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }

    /** Admin/Pimpinan yang menyetujui */
    public function approver()
    {
        return $this->belongsTo(MUser::class, 'approved_by', 'id_user');
    }

    /** Relasi ke approval_pimpinan */
    public function approvalPimpinan()
    {
        return $this->hasOne(ApprovalPimpinan::class, 'id_cuti', 'id_cuti');
    }

    /* ================
     *    SCOPES
     * ================ */
    public function scopeMilikUser($query, int $userId)
    {
        return $query->where('id_user', $userId);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'Menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'Disetujui');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'Ditolak');
    }

    /* =========================
     *   MUTATOR / BUSINESS HELPERS
     * ========================= */
    /**
     * Set status + approver sekaligus (dipakai di AdminCutiController)
     */
    public function setStatus(string $status, ?int $approverId = null): self
    {
        $this->status = $status;
        $this->approved_by = $approverId;
        $this->save();

        return $this;
    }

    /**
     * Hitung lama_cuti (hari) berdasarkan tanggal_mulai & tanggal_selesai (inklusif)
     */
    public function hitungLamaCuti(): void
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            $this->lama_cuti = Carbon::parse($this->tanggal_mulai)
                ->diffInDays(Carbon::parse($this->tanggal_selesai)) + 1;
        }
    }

    /* =========================
     *   MODEL EVENTS
     * ========================= */
    protected static function booted()
    {
        // Default saat create
        static::creating(function (self $model) {
            // default status
            if (empty($model->status)) {
                $model->status = 'Menunggu';
            }
            // tanggal_pengajuan auto jika kosong
            if (empty($model->tanggal_pengajuan)) {
                $model->tanggal_pengajuan = now()->toDateString();
            }
            // lama_cuti auto
            $model->hitungLamaCuti();
        });

        // Rehitung jika tanggal diubah saat update
        static::updating(function (self $model) {
            if ($model->isDirty(['tanggal_mulai', 'tanggal_selesai'])) {
                $model->hitungLamaCuti();
            }
        });
    }
}
    