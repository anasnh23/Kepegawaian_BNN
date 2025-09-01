<?php
// config/attendance.php
return [
    // Hari kerja Senin–Jumat
    'workdays' => [1,2,3,4,5],        // Carbon: 1=Mon ... 7=Sun

    // Jam kerja
    'start_time'   => env('ABSEN_START',   '08:00:00'), // mulai jam kerja
    'tolerance'    => env('ABSEN_TOL',     '00:15:00'), // toleransi keterlambatan (15 menit)
    'end_time'     => env('ABSEN_END',     '17:00:00'), // akhir jam kerja

    // Cut-off penilaian “tidak hadir” untuk HARI INI
    'cutoff_time'  => env('PRESENSI_CUTOFF','17:00:00'),
];
