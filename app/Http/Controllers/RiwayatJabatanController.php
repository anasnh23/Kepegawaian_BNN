<?php

namespace App\Http\Controllers;

use App\Models\RiwayatJabatanModel;
use App\Models\MUser ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatJabatanController extends Controller
{
   
    public function index()
    {
        $user = Auth::user();

        if ($user->id_level == 1) {
            // Admin: Tampilkan semua data
            $riwayatJabatans = RiwayatJabatanModel::with('user')->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat Jabatan Semua Pegawai',
                'list' => ['Dashboard', 'Kepegawaian', 'Riwayat Jabatan']
            ];
        } else {
            // Non-admin: Tampilkan hanya milik sendiri
            $riwayatJabatans = RiwayatJabatanModel::with('user')->where('id_user', $user->id_user)->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat Jabatan Saya',
                'list' => ['Dashboard', 'Kepegawaian', 'Riwayat Jabatan']
            ];
        }

        $activeMenu = 'riwayat_jabatan';

        // Perbaikan: sesuaikan nama folder view dengan underscore
        return view('riwayat_jabatan.index', compact('riwayatJabatans', 'breadcrumb', 'activeMenu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh create
        }
        $users = MUser ::all(); // Ambil data users untuk select
        return view('riwayat_jabatan.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh store
        }
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'nama_jabatan' => 'required|string',
            'tmt_mulai' => 'required|date',
            'tmt_selesai' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        RiwayatJabatanModel::create($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat Jabatan berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $riwayatJabatan = RiwayatJabatanModel::find($id);

        if (!$riwayatJabatan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Cek akses: Non-admin hanya bisa lihat milik sendiri
        if (Auth::user()->id_level != 1 && $riwayatJabatan->id_user != Auth::user()->id_user) {
            abort(403);
        }

        $riwayatJabatan->load('user'); // Load relasi jika diperlukan
        return view('riwayat_jabatan.show', compact('riwayatJabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh edit
        }
        $riwayatJabatan = RiwayatJabatanModel::find($id);

        if (!$riwayatJabatan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $users = MUser ::all(); // Ambil data users untuk select
        return view('riwayat_jabatan.edit', compact('riwayatJabatan', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh update
        }
        $riwayatJabatan = RiwayatJabatanModel::find($id);

        if (!$riwayatJabatan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'nama_jabatan' => 'required|string',
            'tmt_mulai' => 'required|date',
            'tmt_selesai' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $riwayatJabatan->update($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat Jabatan berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh delete
        }
        $riwayatJabatan = RiwayatJabatanModel::find($id);

        if (!$riwayatJabatan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $riwayatJabatan->delete();

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat Jabatan berhasil dihapus.']);
    }
}
