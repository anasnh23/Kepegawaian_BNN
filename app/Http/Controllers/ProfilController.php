<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\MUser;
use Carbon\Carbon;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil user (read-only).
     */
    public function show()
    {
        $user = MUser::with([
            'level',
            'jabatan.refJabatan',
            'pangkat.refPangkat'
        ])->findOrFail(Auth::id());

        // 🔹 Cari TMT awal dari riwayat_jabatan
        $riwayatAwal = DB::table('riwayat_jabatan')
            ->where('id_user', $user->id_user)
            ->orderBy('tmt_mulai', 'asc')
            ->first();

        $masaKerjaTahun = 0;
        $masaKerjaBulan = 0;

        if ($riwayatAwal && $riwayatAwal->tmt_mulai) {
            $tmt = Carbon::parse($riwayatAwal->tmt_mulai);
            $diff = $tmt->diff(Carbon::now());
            $masaKerjaTahun = $diff->y;
            $masaKerjaBulan = $diff->m;
        }

        return view('profil.show', compact('user', 'masaKerjaTahun', 'masaKerjaBulan'));
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $user = MUser::with([
            'level',
            'jabatan.refJabatan',
            'pangkat.refPangkat'
        ])->findOrFail(Auth::id());

        return view('profil.edit', compact('user'));
    }

    /**
     * Menyimpan perubahan data diri dan foto (via form atau AJAX).
     */
    public function update(Request $request)
    {
        $user = MUser::find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        // AJAX upload foto
        if ($request->ajax() && $request->hasFile('foto')) {
            $request->validate([
                'foto' => 'mimes:jpg,jpeg,png,webp,heic,heif|max:2048'
            ]);

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $request->file('foto')->store('foto', 'public');
            $user->save();

            return response()->json(['message' => 'Foto berhasil diperbarui.']);
        }

        // Validasi form data diri
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'no_tlp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string|max:50',
            'foto' => 'nullable|mimes:jpg,jpeg,png,webp,heic,heif|max:2048',
        ]);

        // Simpan data
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_tlp = $request->no_tlp;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->agama = $request->agama;

        // Upload foto via form
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $request->file('foto')->store('foto', 'public');
        }

        $user->save();

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Menyimpan perubahan password secara AJAX.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:3|confirmed',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak cocok.'
            ], 422);
        }

        /** @var \App\Models\MUser $user */
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diperbarui.'
        ]);
    }
}
