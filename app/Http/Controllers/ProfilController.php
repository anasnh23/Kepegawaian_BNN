<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\MUser;

class ProfilController extends Controller
{
    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profil.edit', compact('user'));
    }

    /**
     * Menyimpan perubahan profil.
     */
public function update(Request $request)
{
    $user = MUser::find(Auth::id());
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan.'], 404);
    }

    // ⚠️ Penanganan khusus untuk AJAX foto
    if ($request->ajax() && $request->hasFile('foto')) {
        $request->validate([
            'foto' => 'mimes:jpg,jpeg,png,webp,heic,heif|max:2048'
        ]);

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = $request->file('foto')->store('foto', 'public');
        $user->save();

        return response()->json(['message' => 'Foto berhasil diupload.']);
    }

    // Penanganan form lengkap (bukan AJAX)
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|email',
        'no_tlp' => 'nullable|string|max:20',
        'jenis_kelamin' => 'required|in:L,P',
        'agama' => 'nullable|string|max:50',
        'password' => 'nullable|min:3|confirmed',
        'foto' => 'nullable|mimes:jpg,jpeg,png,webp,heic,heif|max:2048',
    ]);

    $user->nama = $request->nama;
    $user->email = $request->email;
    $user->no_tlp = $request->no_tlp;
    $user->jenis_kelamin = $request->jenis_kelamin;
    $user->agama = $request->agama;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    if ($request->hasFile('foto')) {
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = $request->file('foto')->store('foto', 'public');
    }

    $user->save();

    return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
}
}
