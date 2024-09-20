<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function index()
    {
        // Mengirim data user ke tampilan 'auth.login'
        return view('auth.login');
    }
    public function select_dashboard()
    {
        // Ambil departemen dari pengguna yang sedang login
        $departemen = Auth::user()->departemen;

        // Jika tidak ada departemen, tampilkan halaman dengan pesan error
        if (!$departemen) {
            return view('select-dashboard')->withErrors(['departemen' => 'Departemen tidak ditemukan.']);
        }

        // Tampilkan view select-dashboard
        return view('select-dashboard', compact('departemen'));
    }

    public function login_proses(Request $request)
    {
        $request->validate([
            'npk' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('npk', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Ambil departemen pengguna
            $departemen = $user->departemen;
            if (!$departemen) {
                return redirect()->route('login')->withErrors(['departemen' => 'Departemen tidak ditemukan.']);
            }

            // Arahkan ke route select.dashboard setelah login berhasil
            return redirect()->route('select.dashboard');
        }

        // Jika login gagal, cek jumlah percobaan gagal
        $loginAttempts = session('login_attempts', 0);
        session(['login_attempts' => $loginAttempts + 1]);

        if ($loginAttempts >= 2) {
            // Jika percobaan login sudah 3 kali, tampilkan pesan tunggu
            $lockoutTime = session('lockout_time');
            if ($lockoutTime && now()->lt($lockoutTime)) {
                $remainingTime = $lockoutTime->diffInSeconds(now());
                return back()->withErrors([
                    'login' => "Anda sudah melakukan 3 kali percobaan login. Silakan coba lagi dalam {$remainingTime} detik.",
                ]);
            }

            // Reset percobaan login setelah waktu lockout berakhir
            session(['login_attempts' => 1]);
            session(['lockout_time' => null]);
        } elseif ($loginAttempts >= 2) {
            // Set waktu lockout jika percobaan login mencapai 3
            session(['lockout_time' => now()->addMinutes(1)]);
        }
        dd(session()->all());

        return back()->withErrors([
            'npk' => 'NPK atau Password salah.',
            'password' => 'NPK atau Password salah.',
        ])->withInput();
    }



    public function register_form()
    {
        $departemens = Departemen::all();
        return view('auth.register', compact('departemens'));
    }
    // Fungsi untuk memproses registrasi
    public function register_proses(Request $request)
    {
        $message = [
            'npk.required' => 'NPK tidak boleh kosong.',
            'npk.unique' => 'NPK sudah terdaftar.',
            'departemen.required' => 'Departemen tidak boleh kosong.',
            'departemen.exists' => 'Departemen tidak valid.',
            'name.required' => 'Nama tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.size' => 'Password harus memiliki tepat 8 karakter.',
        ];

        $validator = Validator::make($request->all(), [
            'npk' => 'required|string|max:255|unique:users',
            'departemen' => 'required|exists:departemen,id',
            'name' => 'required|string|max:255',
            'password' => 'required|string|size:8|confirmed',
        ], $message);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $defaultRoleName = 'guest';
        $role = Role::where('name', $defaultRoleName)->first();

        // Buat user baru
        $user = User::create([
            'npk' => $request->npk,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'departemen_id' => $request->departemen,
        ]);

        if ($role) {
            $user->assignRole($role);
        }

        Auth::login($user);

        // Flash message to session
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }


    public function logout()
    {
        Auth::logout();
        session()->forget('active_departemen_id'); // Hapus departemen aktif dari session
        return redirect()->route('login')->with('success', 'you have successfully logged out!');
    }
}
