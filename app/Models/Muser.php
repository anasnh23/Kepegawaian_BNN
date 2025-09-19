<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * Model untuk tabel m_user.
 * PK: id_user
 */
class MUser extends Authenticatable
{
    use HasFactory;

    protected $table      = 'm_user';
    protected $primaryKey = 'id_user';

    // Ubah jika tabel m_user kamu memang punya kolom timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_level',
        'nip',
        'email',
        'nama',
        'username',
        'password',
        'jenis_kelamin',
        'agama',
        'no_tlp',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'id_level' => 'integer',
    ];

    /* =======================
     *         RELASI
     * ======================= */

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'id_level', 'id_level');
    }

    public function pendidikan()
    {
        return $this->hasOne(PendidikanModel::class, 'id_user', 'id_user');
    }

    public function presensi()
    {
        return $this->hasMany(PresensiModel::class, 'id_user', 'id_user');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'id_user', 'id_user');
    }

    public function approvedCuti()
    {
        return $this->hasMany(Cuti::class, 'approved_by', 'id_user');
    }

    public function jabatan()
    {
        return $this->hasOne(JabatanModel::class, 'id_user', 'id_user');
    }

    public function pangkat()
    {
        return $this->hasOne(Pangkat::class, 'id_user', 'id_user');
    }

    public function kgp()
    {
        return $this->hasMany(Kgp::class, 'id_user', 'id_user');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_user', 'id_user');
    }

    public function riwayatGaji()
    {
        return $this->hasMany(RiwayatGajiModel::class, 'id_user', 'id_user');
    }

    public function gajiTerakhir()
    {
        // ambil entri riwayat_gaji terbaru berdasarkan tanggal_berlaku
        return $this->hasOne(RiwayatGajiModel::class, 'id_user', 'id_user')
            ->latestOfMany('tanggal_berlaku');
    }

    /* =======================
     *   ACCESSOR / COMPUTED
     * ======================= */

    /**
     * Estimasi tanggal mulai kerja (aman terhadap kolom yang tidak ada).
     * Prioritas:
     *  - relasi jabatan (tmt_mulai | tmt | tanggal_mulai)
     *  - riwayat_jabatan.min(tmt_mulai | tmt | tanggal_mulai) *cek kolom dulu*
     *  - riwayat_gaji.min(tanggal_berlaku)
     *  - presensi.min(tanggal)
     *  - m_user.created_at
     */
    public function getTanggalMulaiKerjaAttribute()
    {
        // 1) dari relasi jabatan bila ada
        $jabatan = $this->relationLoaded('jabatan') ? $this->jabatan : $this->jabatan()->first();
        if ($jabatan) {
            foreach (['tmt_mulai','tmt','tanggal_mulai'] as $col) {
                if (isset($jabatan->{$col}) && !empty($jabatan->{$col})) {
                    try { return Carbon::parse($jabatan->{$col})->startOfDay(); } catch (\Throwable $e) {}
                }
            }
        }

        // 2) dari riwayat_jabatan (cek tabel/kolom dulu)
        if (Schema::hasTable('riwayat_jabatan')) {
            foreach (['tmt_mulai','tmt','tanggal_mulai'] as $col) {
                if (Schema::hasColumn('riwayat_jabatan', $col)) {
                    try {
                        $val = DB::table('riwayat_jabatan')
                            ->where('id_user', $this->id_user)
                            ->min($col);
                        if ($val) return Carbon::parse($val)->startOfDay();
                    } catch (\Throwable $e) { /* skip */ }
                }
            }
        }

        // 3) earliest tanggal_berlaku di riwayat_gaji
        if (Schema::hasTable('riwayat_gaji') && Schema::hasColumn('riwayat_gaji','tanggal_berlaku')) {
            try {
                $val = DB::table('riwayat_gaji')->where('id_user', $this->id_user)->min('tanggal_berlaku');
                if ($val) return Carbon::parse($val)->startOfDay();
            } catch (\Throwable $e) {}
        }

        // 4) presensi pertama
        if (Schema::hasTable('presensi') && Schema::hasColumn('presensi','tanggal')) {
            try {
                $val = $this->presensi()->min('tanggal');
                if ($val) return Carbon::parse($val)->startOfDay();
            } catch (\Throwable $e) {}
        }

        // 5) fallback terakhir: created_at user (jika ada)
        if (Schema::hasColumn('m_user','created_at') && !empty($this->created_at)) {
            try { return Carbon::parse($this->created_at)->startOfDay(); } catch (\Throwable $e) {}
        }

        return null;
    }

    public function getMasaKerjaYearsAttribute(): int
    {
        $start = $this->tanggal_mulai_kerja;
        if (!$start) return 0;
        $now = Carbon::now('Asia/Jakarta');
        return (int) floor($start->diffInDays($now) / 365.25);
    }

    public function getMasaKerjaLabelAttribute(): string
    {
        $start = $this->tanggal_mulai_kerja;
        if (!$start) return '0 th 0 bln';

        $now    = Carbon::now('Asia/Jakarta');
        $years  = $start->diffInYears($now);
        $months = $start->copy()->addYears($years)->diffInMonths($now);
        return sprintf('%d th %d bln', $years, $months);
    }

    public function getMasaKerjaDetailAttribute(): array
    {
        $start = $this->tanggal_mulai_kerja;
        if (!$start) return ['years'=>0,'months'=>0,'days'=>0];

        $now    = Carbon::now('Asia/Jakarta');
        $years  = $start->diffInYears($now);
        $afterY = $start->copy()->addYears($years);
        $months = $afterY->diffInMonths($now);
        $afterM = $afterY->copy()->addMonths($months);
        $days   = $afterM->diffInDays($now);

        return ['years'=>$years,'months'=>$months,'days'=>$days];
    }

    public function getMasaKerjaDetailLabelAttribute(): string
    {
        $d = $this->masa_kerja_detail;
        return sprintf('%d th %d bln %d hr', $d['years'], $d['months'], $d['days']);
    }

    /* ===== TMK (Tunjangan Masa Kerja) ===== */

    public function getTunjanganMkAttribute(): int
    {
        $rupiahPer4Tahun = (int) env('KGP_NAIK_PER_4_TAHUN', 1000000);
        return intdiv($this->masa_kerja_years, 4) * $rupiahPer4Tahun;
    }

    public function getIsTmkApprovedAttribute(): bool
    {
        try {
            $approvedByStatus = $this->kgp()->whereRaw("LOWER(status)='disetujui'")->exists();
            if ($approvedByStatus) return true;
            return $this->kgp()->whereNotNull('disetujui_oleh')->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getTunjanganMkNominalAttribute(): int
    {
        $rupiahPer4Tahun = (int) env('KGP_NAIK_PER_4_TAHUN', 1000000);
        return intdiv($this->masa_kerja_years, 4) * $rupiahPer4Tahun;
    }

    public function getTunjanganMkSafeAttribute(): ?int
    {
        return $this->is_tmk_approved ? $this->tunjangan_mk_nominal : null;
    }

    public function getTunjanganMkSafeFormattedAttribute(): string
    {
        $val = $this->tunjangan_mk_safe;
        return is_null($val) ? '-' : ('Rp ' . number_format($val, 0, ',', '.'));
    }

    /* ===== GAJI ===== */

    /** Gaji pokok berjalan: ambil dari riwayat terbaru, fallback referensi pangkat */
    public function getGajiPokokAttribute(): ?int
    {
        $terakhir = $this->gajiTerakhir()->first();
        if ($terakhir && !is_null($terakhir->gaji_pokok)) {
            return (int) $terakhir->gaji_pokok;
        }

        try {
            $pangkat = $this->pangkat()->with('refPangkat')->first();
            if ($pangkat && data_get($pangkat, 'refPangkat.gaji_pokok') !== null) {
                return (int) $pangkat->refPangkat->gaji_pokok;
            }
        } catch (\Throwable $e) {}

        return null;
    }

    public function getGajiPokokFormattedAttribute(): string
    {
        $g = $this->gaji_pokok;
        return is_null($g) ? '-' : ('Rp ' . number_format($g, 0, ',', '.'));
    }

    /** Gaji pokok dasar (tanpa pengaruh riwayat) dari referensi golongan */
    public function getGajiPokokDasarAttribute(): ?int
    {
        try {
            $pangkat = $this->pangkat()->with('refPangkat')->first();
            if ($pangkat && $pangkat->refPangkat && $pangkat->refPangkat->gaji_pokok !== null) {
                return (int) $pangkat->refPangkat->gaji_pokok;
            }
        } catch (\Throwable $e) {}
        return null;
    }

    /* =======================
     *        SCOPES
     * ======================= */

    public function scopeLevel($query, $levelId)
    {
        return $query->where('id_level', $levelId);
    }

    public function scopeAdmin($query)
    {
        // sesuaikan ID level admin di sistem kamu
        return $query->where('id_level', 2);
    }

    public function scopePimpinan($query)
    {
        // sesuaikan ID level pimpinan di sistem kamu
        return $query->where('id_level', 3);
    }
}
