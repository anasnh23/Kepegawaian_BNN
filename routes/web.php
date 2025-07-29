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

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Role-based Dashboard Routing
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->id_level == 1) {
        return redirect('/dashboard-admin');
    } elseif ($user->id_level == 2) {
        return redirect('/dashboard-pegawai');
    } elseif ($user->id_level == 3) {
        return redirect('/dashboard-pimpinan');
    }
    abort(403);
})->name('dashboard');

// Login Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postlogin']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // 📊 Dashboards
    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard-pegawai', [DashboardPegawaiController::class, 'index'])->name('dashboard.pegawai');
    Route::get('/dashboard-pimpinan', [DashboardPimpinanController::class, 'index'])->name('dashboard.pimpinan');


    // 👤 Profil
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // 🕒 Presensi
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/presensi-admin', [PresensiAdminController::class, 'index'])->name('presensi.admin');

    // 👥 Manajemen Pegawai & Pangkat (Admin Only)
    Route::resource('/pegawai', PegawaiController::class);
    Route::resource('/pangkat', PangkatController::class);

    // 📄 Cuti Pegawai
    Route::get('/cutipegawai', [CutiController::class, 'index'])->name('cuti.pegawai');
    Route::post('/cuti/store', [CutiController::class, 'store'])->name('cuti.store');
    Route::get('/riwayat-cuti', [CutiController::class, 'riwayat'])->name('cuti.riwayat');
    Route::get('/cuti/cetak/{id}', [CutiController::class, 'cetak'])->name('cuti.cetak');
    Route::post('/cuti/upload-dokumen', [CutiController::class, 'uploadDokumen'])->name('cuti.uploadDokumen');

    // 🔧 Manajemen Cuti oleh Admin
    Route::get('/cutiadmin', [AdminCutiController::class, 'index'])->name('cutiadmin.index');
    Route::post('/cuti/set-status', [AdminCutiController::class, 'setStatus'])->name('cuti.setStatus');
    Route::get('/cuti/edit/{id}', [AdminCutiController::class, 'edit'])->name('cuti.edit');
    Route::put('/cuti/update-status/{id}', [AdminCutiController::class, 'updateStatus'])->name('cuti.updateStatus');

    // 📤 Approval Dokumen oleh Pimpinan
    Route::get('/approval-dokumen', [PimpinanCutiController::class, 'index'])->name('approval.dokumen');
    Route::post('/approval-dokumen/setujui/{id}', [PimpinanCutiController::class, 'approve'])->name('dokumen.setujui');
    Route::post('/approval-dokumen/tolak/{id}', [PimpinanCutiController::class, 'reject'])->name('dokumen.tolak');
    Route::get('/riwayat-approval', [PimpinanCutiController::class, 'riwayat'])->name('riwayat.approval');
    Route::get('/approval-dokumen/edit/{id}', [PimpinanCutiController::class, 'edit'])->name('approval.edit');
Route::post('/approval-dokumen/update-status', [PimpinanCutiController::class, 'updateStatus']);



});
