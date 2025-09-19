<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use App\Models\LevelModel;
use App\Models\PendidikanModel;
use App\Models\JabatanModel;
use App\Models\Kgp;
use App\Models\RefGolonganPangkat;
use App\Models\RefJabatanModel;
use App\Models\Pangkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
public function index()
{
    $pegawai = MUser::with([
        'level',
        'pendidikan',
        'jabatan.refJabatan',
        'pangkat.refPangkat'
    ])->get();

    $levels = LevelModel::all(); // Tambahan
    $jabatans = RefJabatanModel::all(); // Tambahan

    $breadcrumb = (object)[
        'title' => 'Data Pegawai',
        'list' => ['Dashboard', 'Kepegawaian', 'Data Pegawai']
    ];

    return view('pegawai.index', compact('pegawai', 'breadcrumb', 'levels', 'jabatans'))
           ->with('activeMenu', 'pegawai');
}


public function create()
{
    $levels = LevelModel::all();
    $jabatans = RefJabatanModel::all(); // Ganti dari JabatanModel ke Ref
    $pangkats = RefGolonganPangkat::all();

    return view('pegawai.create', compact('levels', 'jabatans', 'pangkats'));
}



 public function store(Request $request)
{
    $request->validate([
        'nip'           => 'required|unique:m_user,nip',
        'nama'          => 'required',
        'email'         => 'required|email|unique:m_user,email',
        'password'      => 'required|min:3',
        'id_level'      => 'required|exists:m_level,id_level',
        'jenis_kelamin' => 'required|in:L,P',
        'foto'          => 'nullable|image|max:2048',
    ]);

    $foto = null;
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto')->store('foto', 'public');
    }

    // === SIMPAN PEGAWAI ===
    $user = MUser::create([
        'id_level'      => $request->id_level,
        'nip'           => $request->nip,
        'email'         => $request->email,
        'nama'          => $request->nama,
        'username'      => $request->username ?? $request->nip,
        'password'      => Hash::make($request->password),
        'jenis_kelamin' => $request->jenis_kelamin,
        'agama'         => $request->agama,
        'no_tlp'        => $request->no_tlp,
        'foto'          => $foto,
    ]);

    // === SIMPAN PENDIDIKAN ===
    if ($request->jenis_pendidikan) {
        PendidikanModel::create([
            'id_user'         => $user->id_user,
            'jenis_pendidikan'=> $request->jenis_pendidikan,
            'tahun_kelulusan' => $request->tahun_kelulusan,
        ]);
    }

    // === SIMPAN JABATAN ===
    if ($request->id_ref_jabatan && $request->tmt_jabatan) {
        JabatanModel::create([
            'id_user'        => $user->id_user,
            'id_ref_jabatan' => $request->id_ref_jabatan,
            'tmt'            => $request->tmt_jabatan,
        ]);
    }

    // === SIMPAN PANGKAT + GAJI ===
    if ($request->id_ref_pangkat && $request->tmt_pangkat) {
        $pangkat = Pangkat::create([
            'id_user'        => $user->id_user,
            'id_ref_pangkat' => $request->id_ref_pangkat,
            'id_jabatan'     => null,
            'gaji_pokok'     => $request->gaji_pokok ?? 0, // <== tambahkan gaji pokok di sini
        ]);

        // Simpan KGP
        Kgp::create([
            'id_user'   => $user->id_user,
            'tahun_kgp' => date('Y', strtotime($request->tmt_pangkat)),
            'tmt'       => $request->tmt_pangkat,
        ]);
    }

    return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil disimpan');
}



public function edit($id)
{
    $pegawai = MUser::with(['pendidikan', 'jabatan', 'pangkat'])->findOrFail($id);
    $levels = LevelModel::all();
    $jabatans = RefJabatanModel::all();
    $pangkats = RefGolonganPangkat::all();

    return view('pegawai.edit', compact('pegawai', 'levels', 'jabatans', 'pangkats'));
}


public function update(Request $request, $id)
{
    $user = MUser::findOrFail($id);

    $request->validate([
        'nip'          => 'required|unique:m_user,nip,' . $id . ',id_user',
        'email'        => 'required|email|unique:m_user,email,' . $id . ',id_user',
        'nama'         => 'required',
        'id_level'     => 'required|exists:m_level,id_level',
        'jenis_kelamin'=> 'required|in:L,P',
        'foto'         => 'nullable|image|max:2048',
    ]);

    // === FOTO ===
    if ($request->hasFile('foto')) {
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }
        $user->foto = $request->file('foto')->store('foto', 'public');
    }

    // === DATA DASAR ===
    $user->id_level      = $request->id_level;
    $user->nip           = $request->nip;
    $user->email         = $request->email;
    $user->nama          = $request->nama;
    $user->username      = $request->username;         // dipakai juga untuk reset pass
    $user->jenis_kelamin = $request->jenis_kelamin;
    $user->agama         = $request->agama;
    $user->no_tlp        = $request->no_tlp;

    // === PASSWORD HANDLING ===
    // Prioritas:
    // 1) Jika reset_password=1 → set password = username (hash).
    // 2) Kalau admin isi password manual → pakai itu.
    // 3) Selain itu → password tidak diubah.
    if ($request->boolean('reset_password')) {
        if (!empty($request->username)) {
            $user->password = Hash::make($request->username);
        }
        // jika username kosong, biarkan password lama (bisa tambahkan guard kalau mau)
    } elseif ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    // === PENDIDIKAN ===
    if ($request->filled('jenis_pendidikan') || $request->filled('tahun_kelulusan')) {
        $pendidikan = PendidikanModel::firstOrNew(['id_user' => $user->id_user]);
        $pendidikan->jenis_pendidikan = $request->jenis_pendidikan;
        $pendidikan->tahun_kelulusan  = $request->tahun_kelulusan;
        $pendidikan->save();
    }

    // === JABATAN ===
    if ($request->filled('id_ref_jabatan') || $request->filled('tmt_jabatan')) {
        $jabatan = JabatanModel::firstOrNew(['id_user' => $user->id_user]);
        $jabatan->id_ref_jabatan = $request->id_ref_jabatan;
        $jabatan->tmt            = $request->tmt_jabatan;
        $jabatan->save();
    }

    // === PANGKAT ===
    if ($request->filled('id_ref_pangkat')) {
        $pangkat = Pangkat::firstOrNew(['id_user' => $user->id_user]);
        $pangkat->id_ref_pangkat = $request->id_ref_pangkat;
        $pangkat->save();
    }

    // Tambahkan entri KGP jika ada TMT pangkat
    if ($request->filled('id_ref_pangkat') && $request->filled('tmt_pangkat')) {
        Kgp::create([
            'id_user'    => $user->id_user,
            'tahun_kgp'  => date('Y', strtotime($request->tmt_pangkat)),
            'tmt'        => $request->tmt_pangkat,
        ]);
    }

    return response()->json(['message' => 'Data pegawai berhasil diperbarui.']);
}
    

    public function show($id)
    {
        $pegawai = MUser::with(['level', 'pendidikan', 'jabatan.refJabatan', 'pangkat.refPangkat'])->findOrFail($id);
        return view('pegawai.show', compact('pegawai'));
    }

    public function destroy($id)
    {
        $pegawai = MUser::findOrFail($id);

        if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
            Storage::disk('public')->delete($pegawai->foto);
        }

        PendidikanModel::where('id_user', $pegawai->id_user)->delete();
        JabatanModel::where('id_user', $pegawai->id_user)->delete();
        Pangkat::where('id_user', $pegawai->id_user)->delete();

        $pegawai->delete();

        return response()->json(['message' => 'Data pegawai berhasil dihapus.']);
    }
}
