<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCutiController extends Controller
{
    public function index()
    {
        // Ambil semua cuti + relasi user (pegawai)
        $cuti = Cuti::with('pegawai')->orderBy('created_at', 'desc')->get();

        $breadcrumb = (object)[
            'title' => 'Manajemen Cuti Pegawai',
            'list' => ['Dashboard', 'Kepegawaian', 'Manajemen Cuti']
        ];

        return view('cuti.admin', compact('cuti', 'breadcrumb'))->with('activeMenu', 'cuti');
    }

    public function setStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cuti,id_cuti',
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $cuti = Cuti::findOrFail($request->id);
        $cuti->status = $request->status;
        $cuti->approved_by = Auth::user()->id_user;
        $cuti->updated_at = now();
        $cuti->save();

        return response()->json(['message' => 'Status cuti berhasil diperbarui.']);
    }

    public function edit($id)
{
    $cuti = Cuti::with('pegawai')->findOrFail($id);

    $breadcrumb = (object)[
        'title' => 'Edit Status Cuti',
        'list' => ['Dashboard', 'Kepegawaian', 'Manajemen Cuti', 'Edit Cuti']
    ];

    return view('cuti.editcuti', compact('cuti', 'breadcrumb'))->with('activeMenu', 'cuti');
}

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Disetujui,Ditolak,Menunggu',
    ]);

    $cuti = Cuti::findOrFail($id);
    $cuti->status = $request->status;
    $cuti->approved_by = Auth::user()->id_user;
    $cuti->updated_at = now();
    $cuti->save();

    return redirect('/cuti')->with('success', 'Status cuti berhasil diperbarui.');
}

}
