<?php

namespace App\Http\Controllers;

use App\Models\Kgp;
use App\Models\MUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KgpController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->id_level == 1) {
            // Admin: Tampilkan semua data
            $kgps = Kgp::with('pegawai')->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat KGP Semua Pegawai',
                'list' => ['Dashboard', 'Kepegawaian', 'Riwayat KGP']
            ];
        } else {
            // Non-admin: Tampilkan hanya milik sendiri
            $kgps = Kgp::with('pegawai')->where('id_user', $user->id_user)->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat KGP Saya',
                'list' => ['Dashboard', 'Kepegawaian', 'Riwayat KGP']
            ];
        }

        $activeMenu = 'kgp';

        return view('kgp.index', compact('kgps', 'breadcrumb', 'activeMenu'));
    }

    public function create()
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh create
        }
        $users = MUser::all(); // Ambil data users untuk select
        return view('kgp.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh store
        }
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'tahun_kgp' => 'required|integer|min:1900|max:2100', // Asumsi tahun adalah integer, sesuaikan jika perlu
            'tmt' => 'required|date',
        ]);

        Kgp::create($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat KGP berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $kgp = Kgp::find($id);

        if (!$kgp) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Cek akses: Non-admin hanya bisa lihat milik sendiri
        if (Auth::user()->id_level != 1 && $kgp->id_user != Auth::user()->id_user) {
            abort(403);
        }

        $kgp->load('pegawai'); // Load relasi jika diperlukan
        return view('kgp.show', compact('kgp'));
    }

    public function edit($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh edit
        }
        $kgp = Kgp::find($id);

        if (!$kgp) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $users = MUser::all(); // Ambil data users untuk select
        return view('kgp.edit', compact('kgp', 'users'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh update
        }
        $kgp = Kgp::find($id);

        if (!$kgp) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'tahun_kgp' => 'required|integer|min:1900|max:2100', // Asumsi tahun adalah integer, sesuaikan jika perlu
            'tmt' => 'required|date',
        ]);

        $kgp->update($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat KGP berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh delete
        }
        $kgp = Kgp::find($id);

        if (!$kgp) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $kgp->delete();

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat KGP berhasil dihapus.']);
    }
}
