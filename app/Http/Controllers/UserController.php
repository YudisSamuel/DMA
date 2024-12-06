<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // Tampilkan halaman daftar pengguna
    public function index()
    {
        $users = User::all();
        return view('users.user', compact('users')); // sesuaikan dengan 'user/index.blade.php'
    }

    // Menampilkan halaman edit user
    public function edit($id)
    {
        $user = User::findOrFail($id); // Ambil data user berdasarkan ID
        return view('users.edit', compact('user')); // Kirim data user ke view
    }

    // Proses update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|alpha_num|min:3|max:255|unique:tb_pengguna,username,' . $id . ',id_pgn',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Hapus foto lama jika ada foto baru
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama
            if ($user->profile_photo) {
                Storage::delete('public/profile_photos/' . $user->profile_photo);
            }

            // Simpan foto baru
            $fotoName = time() . '_' . uniqid() . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $fotoPath = $request->file('profile_photo')->storeAs('public/profile_photos', $fotoName);
            $user->profile_photo = basename($fotoPath);
        }

        $user->username = $request->username;
        $user->save();

        return redirect()->route('user')->with('success', 'Pengguna berhasil diperbarui.');
    }

    // Proses hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Hapus foto profil jika ada
        if ($user->profile_photo) {
            Storage::delete('public/profile_photos/' . $user->profile_photo);
        }

        $user->delete();

        return redirect()->route('user')->with('success', 'Pengguna berhasil dihapus.');
    }
}
