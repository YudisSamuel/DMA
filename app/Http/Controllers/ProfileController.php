<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'old_password' => 'nullable|min:8',
            'new_password' => 'nullable|min:8|confirmed', // "confirmed" otomatis memeriksa kesesuaian dengan input "new_password_confirmation"
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->username = $request->name;

        // Validasi dan update password
        if ($request->filled('old_password')) {
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return redirect()->back()->withErrors(['old_password' => 'Old password is incorrect']);
            }
        }

        // Update foto profil jika disediakan
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::delete('public/' . $user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

}
