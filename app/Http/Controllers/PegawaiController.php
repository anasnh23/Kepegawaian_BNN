<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use App\Models\LevelModel;
use App\Models\PendidikanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = MUser::with(['level', 'pendidikan'])->get();

        $breadcrumb = (object)[
            'title' => 'Data Pegawai',
            'list' => ['Dashboard', 'Kepegawaian', 'Data Pegawai']
        ];

        return view('pegawai.index', compact('pegawai', 'breadcrumb'))->with('activeMenu', 'pegawai');
    }

    public function create()
    {
        $levels = LevelModel::all();
        return view('pegawai.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:m_user,nip',
            'nama' => 'required',
            'email' => 'required|email|unique:m_user,email',
            'password' => 'required|min:3',
            'id_level' => 'required|exists:m_level,id_level',
            'jenis_kelamin' => 'required|in:L,P',
            'foto' => 'nullable|image|max:2048',
        ]);

        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('foto', 'public');
        }

        $user = MUser::create([
            'id_level' => $request->id_level,
            'nip' => $request->nip,
            'email' => $request->email,
            'nama' => $request->nama,
            'username' => $request->username ?? $request->nip,
            'password' => Hash::make($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'no_tlp' => $request->no_tlp,
            'foto' => $foto,
        ]);

        if ($request->jenis_pendidikan) {
            PendidikanModel::create([
                'id_user' => $user->id_user,
                'jenis_pendidikan' => $request->jenis_pendidikan,
                'tahun_kelulusan' => $request->tahun_kelulusan,
            ]);
        }

        return response()->json(['message' => 'Berhasil menambahkan data pegawai.']);
    }

    public function edit($id)
    {
        $pegawai = MUser::with('pendidikan')->findOrFail($id);
        $levels = LevelModel::all();

        return view('pegawai.edit', compact('pegawai', 'levels'));
    }

    public function update(Request $request, $id)
    {
        $user = MUser::findOrFail($id);

        $request->validate([
            'nip' => 'required|unique:m_user,nip,' . $id . ',id_user',
            'email' => 'required|email|unique:m_user,email,' . $id . ',id_user',
            'nama' => 'required',
            'id_level' => 'required|exists:m_level,id_level',
            'jenis_kelamin' => 'required|in:L,P',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Handle foto baru
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('foto', 'public');
        }

        // Update data user
        $user->update([
            'id_level' => $request->id_level,
            'nip' => $request->nip,
            'email' => $request->email,
            'nama' => $request->nama,
            'username' => $request->username,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'no_tlp' => $request->no_tlp,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Update pendidikan
        if ($request->jenis_pendidikan) {
            $pendidikan = PendidikanModel::firstOrNew(['id_user' => $user->id_user]);
            $pendidikan->jenis_pendidikan = $request->jenis_pendidikan;
            $pendidikan->tahun_kelulusan = $request->tahun_kelulusan;
            $pendidikan->save();
        }

        return response()->json(['message' => 'Data pegawai berhasil diperbarui.']);
    }

    public function show($id)
{
    $pegawai = MUser::with(['level', 'pendidikan'])->findOrFail($id);
    return view('pegawai.show', compact('pegawai'));
}

public function destroy($id)
{
    $pegawai = MUser::findOrFail($id);

    // Hapus file foto jika ada
    if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
        Storage::disk('public')->delete($pegawai->foto);
    }

    // Hapus data pendidikan jika ada
    PendidikanModel::where('id_user', $pegawai->id_user)->delete();

    // Hapus data pegawai
    $pegawai->delete();

    return response()->json(['message' => 'Data pegawai berhasil dihapus.']);
}


}
