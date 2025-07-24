<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PresensiController; 
use App\Http\Controllers\PresensiAdminController;


Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postlogin']);

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [WelcomeController::class, 'index'])->name('dashboard');
    Route::get('/welcome', [WelcomeController::class, 'index']);

    Route::resource('/pegawai', PegawaiController::class);
    Route::resource('/pangkat', PangkatController::class);

    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // ✅ ROUTE PRESENSI
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');

    // Admin - Data Presensi
    Route::get('/presensi-admin', [PresensiAdminController::class, 'index'])->name('presensi.admin');

});
