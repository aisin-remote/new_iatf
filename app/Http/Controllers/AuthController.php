<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return view('select-dashboard');
    }
    public function login_proses(Request $request)
    {
        // Validasi input
        $request->validate([
            'npk' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('npk', 'password');

        // Rate Limiting
        $attemptsKey = 'login_attempts_' . $request->ip();
        $lockoutKey = 'login_lockout_' . $request->ip();

        if (RateLimiter::tooManyAttempts($attemptsKey, 3)) {
            $seconds = RateLimiter::availableIn($attemptsKey);
            return back()->withErrors(['lockout' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.']);
        }

        if (Auth::attempt($credentials)) {
            // Reset the rate limiter on successful login
            RateLimiter::clear($attemptsKey);
            return redirect()->route('select.dashboard');
        }

        // Increment login attempts
        RateLimiter::hit($attemptsKey, 60); // Set 1-minute expiration for attempts

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
            'password.size' => 'Password harus memiliki tepat 8 karakter.', // Pesan kesalahan untuk panjang tepat 8 karakter
        ];

        $validator = Validator::make($request->all(), [
            'npk' => 'required|string|max:255|unique:users',
            'departemen' => 'required|exists:departemen,id',
            'name' => 'required|string|max:255',
            'password' => 'required|string|size:8|confirmed', // Mengatur panjang tepat 8 karakter
        ], $message);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $defaultRoleName = 'guest';
        $role = Role::where('name', $defaultRoleName)->first();

        $user = User::create([
            'npk' => $request->npk,
            'departemen_id' => $request->departemen,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        if ($role) {
            $user->assignRole($role);
        }

        Auth::login($user);

        Alert::success('Success', 'Registration successful! Please login.');
        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'you have successfully logged out!');
    }
}
