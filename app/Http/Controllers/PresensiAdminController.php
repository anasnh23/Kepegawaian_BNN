<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiModel;

class PresensiAdminController extends Controller
{
    public function index()
    {
        $data = PresensiModel::with('user')->latest()->get();

        $breadcrumb = (object)[
            'title' => 'Data Presensi',
            'list' => ['Dashboard', 'Kepegawaian', 'Data Presensi']
        ];

        return view('presensi.admin', compact('data', 'breadcrumb'))->with('activeMenu', 'presensi-admin');
    }
}
