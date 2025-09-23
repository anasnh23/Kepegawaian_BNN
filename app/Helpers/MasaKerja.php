<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MasaKerja
{
    /** Ambil tanggal mulai kerja paling awal yang valid untuk user. */
    public static function tanggalMulai(int $userId): ?Carbon
    {
        // 1) riwayat_jabatan (cek kolom yang ada)
        if (Schema::hasTable('riwayat_jabatan')) {
            foreach (['tmt_mulai', 'tmt', 'tanggal_mulai'] as $col) {
                if (Schema::hasColumn('riwayat_jabatan', $col)) {
                    $val = DB::table('riwayat_jabatan')->where('id_user', $userId)->min($col);
                    if ($val) { try { return Carbon::parse($val)->startOfDay(); } catch (\Throwable $e) {} }
                }
            }
        }

        // 2) kgp.tmt
        if (Schema::hasTable('kgp') && Schema::hasColumn('kgp','tmt')) {
            $val = DB::table('kgp')->where('id_user', $userId)->min('tmt');
            if ($val) { try { return Carbon::parse($val)->startOfDay(); } catch (\Throwable $e) {} }
        }

        // 3) riwayat_gaji.tanggal_berlaku
        if (Schema::hasTable('riwayat_gaji') && Schema::hasColumn('riwayat_gaji','tanggal_berlaku')) {
            $val = DB::table('riwayat_gaji')->where('id_user', $userId)->min('tanggal_berlaku');
            if ($val) { try { return Carbon::parse($val)->startOfDay(); } catch (\Throwable $e) {} }
        }

        // 4) presensi.tanggal
        if (Schema::hasTable('presensi') && Schema::hasColumn('presensi','tanggal')) {
            try {
                $val = DB::table('presensi')->where('id_user', $userId)->min('tanggal');
                if ($val) return Carbon::parse($val)->startOfDay();
            } catch (\Throwable $e) {}
        }

        // 5) m_user.tanggal_masuk → created_at
        if (Schema::hasTable('m_user')) {
            if (Schema::hasColumn('m_user','tanggal_masuk')) {
                $val = DB::table('m_user')->where('id_user', $userId)->value('tanggal_masuk');
                if ($val) { try { return Carbon::parse($val)->startOfDay(); } catch (\Throwable $e) {} }
            }
            if (Schema::hasColumn('m_user','created_at')) {
                $val = DB::table('m_user')->where('id_user', $userId)->value('created_at');
                if ($val) { try { return Carbon::parse($val)->startOfDay(); } catch (\Throwable $e) {} }
            }
        }

        return null;
    }

    /** Total masa kerja (tahun, dibulatkan ke bawah). */
    public static function years(int $userId): int
    {
        $start = self::tanggalMulai($userId);
        if (!$start) return 0;
        return (int) $start->diffInYears(Carbon::now('Asia/Jakarta'));
    }

    /** Tahun & bulan untuk tampilan (opsional dipakai di dashboard). */
    public static function yearsMonths(int $userId): array
    {
        $start = self::tanggalMulai($userId);
        if (!$start) return [0, 0];

        $now    = Carbon::now('Asia/Jakarta');
        $years  = $start->diffInYears($now);
        $months = $start->copy()->addYears($years)->diffInMonths($now);
        return [$years, $months];
    }

    public static function label(int $userId): string
{
    [$years, $months] = self::yearsMonths($userId);

    if ($years === 0 && $months === 0) {
        return "Baru Bergabung";
    }

    $parts = [];
    if ($years > 0) {
        $parts[] = $years . ' th';
    }
    if ($months > 0) {
        $parts[] = $months . ' bln';
    }

    return implode(' ', $parts);
}

}
