<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Memastikan akun Admin Utama selalu ada di dalam sistem
    private function ensureMainAdminExists(): void
    {
        if (!User::where('username', 'Maulana321')->exists()) {
            User::create([
                'name' => 'Maulana (Admin Utama)',
                'username' => 'Maulana321',
                'email' => 'admin@sistemarsip.local',
                'password' => Hash::make('naazneen123'),
                'role' => 'admin',
                'is_approved' => true,
            ]);
        }
    }

    public function showLogin()
    {
        $this->ensureMainAdminExists();
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->ensureMainAdminExists();

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'Username atau password yang Anda masukkan salah.',
            ])->onlyInput('username');
        }

        // Cek apakah akun sudah disetujui oleh Admin Utama
        if (!$user->is_approved) {
            return back()->withErrors([
                'username' => 'Akun Anda belum aktif. Silakan tunggu persetujuan dari Admin Utama.',
            ])->onlyInput('username');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,karyawan',
        ], [
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah (tanpa spasi).',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
        ]);

        User::create([
            'name' => $validated['username'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_approved' => false, // Wajib menunggu persetujuan Admin Utama
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil dikirim! Akun Anda menunggu persetujuan Admin Utama.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Halaman daftar persetujuan akun (Khusus Admin)
    public function approvals()
    {
        $pendingUsers = User::where('is_approved', false)->latest()->get();
        $approvedUsers = User::where('is_approved', true)->latest()->get();

        return view('auth.approvals', compact('pendingUsers', 'approvedUsers'));
    }

    // Menyetujui pendaftaran akun
    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', "Akun {$user->username} berhasil disetujui sebagai " . ucfirst($user->role) . ".");
    }

    // Menolak atau menghapus akun
    public function reject(User $user)
    {
        if ($user->username === 'Maulana321') {
            return back()->withErrors(['error' => 'Akun Admin Utama tidak dapat dihapus.']);
        }

        $username = $user->username;
        $user->delete();

        return back()->with('success', "Permintaan/akun {$username} telah ditolak dan dihapus.");
    }
}