<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LevelModel; // Asumsi ada LevelModel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Menampilkan daftar user
    public function index()
    {
        $users = UserModel::with('level')->get(); // Ambil semua user dengan relasi level
        return view('user.index', compact('users'));
    }

    // Form create user
    public function create()
    {
        $levels = LevelModel::all(); // Ambil semua level untuk dropdown
        return view('user.create', compact('levels'));
    }

    // Store user baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_level' => 'required|exists:m_level,id_level',
            'nip' => 'required|string|max:50|unique:m_user,nip',
            'email' => 'nullable|email|max:100|unique:m_user,email',
            'nama' => 'nullable|string|max:100',
            'username' => 'nullable|string|max:50|unique:m_user,username',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'no_tlp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password); // Hash password

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('photos', 'public'); // Simpan di storage/public/photos
        }

        UserModel::create($data);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    // Form edit user
    public function edit($id_user)
    {
        $user = UserModel::findOrFail($id_user);
        $levels = LevelModel::all();
        return view('user.edit', compact('user', 'levels'));
    }

    // Update user
    public function update(Request $request, $id_user)
    {
        $user = UserModel::findOrFail($id_user);

        $validator = Validator::make($request->all(), [
            'id_level' => 'required|exists:m_level,id_level',
            'nip' => 'required|string|max:50|unique:m_user,nip,' . $id_user . ',id_user',
            'email' => 'nullable|email|max:100|unique:m_user,email,' . $id_user . ',id_user',
            'nama' => 'nullable|string|max:100',
            'username' => 'nullable|string|max:50|unique:m_user,username,' . $id_user . ',id_user',
            'password' => 'nullable|string|min:8', // Password optional saat update
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'no_tlp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']); // Jangan update password jika kosong
        }

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            $data['foto'] = $request->file('foto')->store('photos', 'public');
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate');
    }

    // Delete user
    public function destroy($id_user)
    {
        $user = UserModel::findOrFail($id_user);

        // Hapus foto jika ada
        if ($user->foto && Storage::exists('public/' . $user->foto)) {
            Storage::delete('public/' . $user->foto);
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }
}