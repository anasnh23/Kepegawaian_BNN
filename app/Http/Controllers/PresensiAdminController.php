<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiModel;

class PresensiAdminController extends Controller
{
    public function index()
    {
        $data = PresensiModel::with('user')->latest()->get();
        $activeMenu = 'presensi-admin';
        return view('presensi.admin', compact('data', 'activeMenu'));
    }
}
