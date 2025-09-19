<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PresensiModel extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = false; // tabel tidak punya updated_at

    protected $fillable = [
        'id_user', 'tanggal',
        'jam_masuk', 'foto_masuk', 'lat_masuk', 'long_masuk',
        'jam_pulang', 'foto_pulang', 'lat_pulang', 'long_pulang',
        'status',
        'lokasi',
        'created_at',
    ];

    /**
     * Kolom virtual yang otomatis tersedia di $model->toArray()
     * & bisa dipakai di Blade: $row->foto_masuk_url / $row->foto_pulang_url
     */
    protected $appends = ['foto_masuk_url', 'foto_pulang_url'];

    /* =======================
     * Relasi
     * ======================= */
    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }

    /* =======================
     * Accessor URL Foto
     * ======================= */

    /** Normalisasi path apapun menjadi URL yang valid untuk <img src="..."> */
    protected function toUrl(?string $path): ?string
    {
        if (!$path) return null;

        // Sudah URL absolut
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        // Jika tersimpan sebagai "/storage/..."
        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        // Pastikan relatif ke folder presensi/
        $clean = ltrim($path, '/');
        if (!str_starts_with($clean, 'presensi/')) {
            $clean = 'presensi/' . $clean;
        }

        // Jika file ada di disk public, kembalikan URL publiknya
        if (Storage::disk('public')->exists($clean)) {
            return Storage::url($clean); // -> "/storage/presensi/xxx.jpg"
        }

        // Fallback: tetap kembalikan Storage::url agar <img> tetap mencoba memuat
        return Storage::url($clean);
    }

    public function getFotoMasukUrlAttribute(): ?string
    {
        return $this->toUrl($this->foto_masuk);
    }

    public function getFotoPulangUrlAttribute(): ?string
    {
        return $this->toUrl($this->foto_pulang);
    }

    /* =======================
     * Mutator penyimpanan path
     * (opsional tapi dianjurkan, memastikan DB menyimpan path relatif)
     * ======================= */
    public function setFotoMasukAttribute($value): void
    {
        $this->attributes['foto_masuk'] = $this->normalizePath($value);
    }

    public function setFotoPulangAttribute($value): void
    {
        $this->attributes['foto_pulang'] = $this->normalizePath($value);
    }

    /** Simpan hanya "presensi/xxx.jpg" meskipun input "/storage/presensi/xxx.jpg" atau URL */
    protected function normalizePath(?string $value): ?string
    {
        if (!$value) return null;

        // Jika URL absolut: ambil nama filenya saja
        if (preg_match('~^https?://~i', $value)) {
            $basename = basename(parse_url($value, PHP_URL_PATH));
            return 'presensi/' . $basename;
        }

        // Hilangkan prefix "/storage/"
        $clean = ltrim($value, '/');
        $clean = preg_replace('~^storage/~', '', $clean);

        // Pastikan prefix presensi/
        if (!str_starts_with($clean, 'presensi/')) {
            $clean = 'presensi/' . $clean;
        }

        return $clean;
    }
}
