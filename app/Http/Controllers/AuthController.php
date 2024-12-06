<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('login');
    }

    public function username()
    {
        return 'username';
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            logger('User tidak ditemukan');
            return back()->withErrors(['username' => 'User tidak ditemukan']);
        }

        if (Hash::check($request->password, $user->password)) {
            logger('Password cocok, login berhasil');
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('Home');
        } else {
            logger('Password tidak cocok');
            return back()->withErrors(['password' => 'Password salah']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirect ke halaman login setelah logout
    }

    // Menampilkan halaman registrasi
    public function showRegistrationForm()
    {
        return view('register'); // Pastikan ada view 'register.blade.php'
    }

    // Proses registrasi
    public function register(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'username' => 'required|unique:tb_pengguna|alpha_num|min:3|max:255',
                'password' => 'required|min:6|confirmed',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Pastikan folder exists dengan cara lebih aman
            $profilePhotosPath = storage_path('app/public/profile_photos');
            if (!File::exists($profilePhotosPath)) {
                File::makeDirectory($profilePhotosPath, 0755, true);
            }

            // Handle foto profil
            $fotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $foto = $request->file('profile_photo');
                $fotoName = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();

                // Simpan file dengan metode yang lebih robust
                $fotoPath = $foto->storeAs('public/profile_photos', $fotoName);
            }

            // Buat user baru
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'profile_photo' => $fotoPath ? basename($fotoPath) : null,
            ]);

            return redirect('/')
                   ->with('success', 'Registration successful! Please login.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani kesalahan validasi secara spesifik
            return back()
                   ->withErrors($e->validator)
                   ->withInput($request->except('password', 'password_confirmation'));

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Registration error: ' . $e->getMessage());

            return back()
                   ->with('error', 'An error occurred: ' . $e->getMessage())
                   ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
