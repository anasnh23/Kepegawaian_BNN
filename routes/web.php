<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PegawaiController;

Route::get('/', [WelcomeController::class, 'index']);
Route::resource('/pegawai', PegawaiController::class);
