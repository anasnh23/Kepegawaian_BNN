<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardPegawaiController;
use App\Http\Controllers\DashboardPimpinanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PresensiAdminController;
use App\Http\Controllers\PimpinanCutiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\AdminCutiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\RefJabatanController;
use App\Http\Controllers\RiwayatGajiController;
use App\Http\Controllers\ApprovalKgpController;
// Redirect root
Route::get('/', function () {
    if (Auth::check()) return redirect('/dashboard');
    return redirect('/login');
});

// Dashboard per level
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->id_level == 1) return redirect('/dashboard-admin');
    if ($user->id_level == 2) return redirect('/dashboard-pegawai');
    if ($user->id_level == 3) return redirect('/dashboard-pimpinan');
    abort(403);
})->name('dashboard');

// Login/logout
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postlogin']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Group routes untuk pengguna login
Route::middleware(['auth'])->group(function () {

    // Dashboards
    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard-pegawai', [DashboardPegawaiController::class, 'index'])->name('dashboard.pegawai');
    Route::get('/dashboard-pimpinan', [DashboardPimpinanController::class, 'index'])->name('dashboard.pimpinan');

    // Profil
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.updatePassword');

    // Presensi
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/presensi-admin', [PresensiAdminController::class, 'index'])->name('presensi.admin');
    Route::get('/presensi/export', [PresensiAdminController::class, 'exportExcel'])->name('presensi.export');

    // Pegawai & Pangkat
    Route::resource('/pegawai', PegawaiController::class);
    Route::resource('/pangkat', PangkatController::class);

    // Cuti Pegawai
    Route::get('/cutipegawai', [CutiController::class, 'index'])->name('cuti.pegawai');
    Route::post('/cuti/store', [CutiController::class, 'store'])->name('cuti.store');
    Route::get('/riwayat-cuti', [CutiController::class, 'riwayat'])->name('cuti.riwayat');
    Route::get('/cuti/cetak/{id}', [CutiController::class, 'cetak'])->name('cuti.cetak');
    Route::post('/cuti/upload-dokumen', [CutiController::class, 'uploadDokumen'])->name('cuti.uploadDokumen');

    // Manajemen Cuti oleh Admin
    Route::get('/cutiadmin', [AdminCutiController::class, 'index'])->name('cutiadmin.index');
    Route::post('/cuti/set-status', [AdminCutiController::class, 'setStatus'])->name('cuti.setStatus');
    Route::get('/cuti/edit/{id}', [AdminCutiController::class, 'edit'])->name('cuti.edit');
    Route::put('/cuti/update-status/{id}', [AdminCutiController::class, 'updateStatus'])->name('cuti.updateStatus');

    // Approval Dokumen oleh Pimpinan
    Route::get('/approval-dokumen', [PimpinanCutiController::class, 'index'])->name('approval.dokumen');
    Route::post('/approval-dokumen/setujui/{id}', [PimpinanCutiController::class, 'approve'])->name('dokumen.setujui');
    Route::post('/approval-dokumen/tolak/{id}', [PimpinanCutiController::class, 'reject'])->name('dokumen.tolak');
    Route::get('/riwayat-approval', [PimpinanCutiController::class, 'riwayat'])->name('riwayat.approval');
    Route::get('/approval-dokumen/edit/{id}', [PimpinanCutiController::class, 'edit'])->name('approval.edit');
    Route::post('/approval-dokumen/update-status', [PimpinanCutiController::class, 'updateStatus']);

    // 🔔 NOTIFIKASI
    Route::get('/notifikasi', [NotifikasiController::class, 'semua'])->name('notifikasi.semua'); 
    Route::get('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca.id');
    Route::post('/notifikasi/baca', [NotifikasiController::class, 'tandaiSemua'])->name('notifikasi.baca');
    Route::get('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca.id'); 

    // Jabatan
    Route::resource('/ref_jabatan', RefJabatanController::class);

    // Riwayat Gaji
    Route::resource('/riwayat_gaji', RiwayatGajiController::class);

     Route::get('/approval-kgp', [ApprovalKgpController::class, 'index'])->name('approval.kgb');
    Route::post('/approval-kgp/{id}/approve', [ApprovalKgpController::class, 'approve'])->name('approval.kgb.approve');
    Route::post('/approval-kgp/{id}/reject', [ApprovalKgpController::class, 'reject'])->name('approval.kgb.reject');



});
