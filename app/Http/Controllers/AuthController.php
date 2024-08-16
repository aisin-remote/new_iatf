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
        // Ambil departemen dari session
        $departmens = session('departmens', []);

        // Jika hanya ada satu departemen, langsung set sebagai default dan redirect ke dashboard
        if (count($departmens) == 1) {
            $departmen = $departmens->first();
            session(['active_departemen_id' => $departmen->id]);
            return redirect()->route('dashboard');
        }

        // Jika lebih dari satu, tampilkan pilihan departemen di select-dashboard
        return view('select-dashboard', compact('departmens'));
    }
    public function switchDepartemen($id)
    {
        $user = Auth::user();
        $departmen = $user->departmens()->find($id);

        if ($departmen) {
            $user->selected_departemen_id = $departmen->id;
            $user->save();
        }

        return redirect()->back();
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

            // Pastikan departmens tidak null
            $departmens = $user->departmens ?? collect();

            // Simpan daftar departemen di session
            session(['departmens' => $departmens]);

            // Jika pengguna tidak memiliki departemen, arahkan ke halaman select-dashboard
            if ($departmens->isEmpty()) {
                return redirect()->route('select.dashboard');
            } elseif ($departmens->count() > 1) {
                return redirect()->route('select.dashboard');
            } else {
                // Jika hanya ada satu departemen, arahkan langsung ke dashboard
                $departmen = $departmens->first();

                if ($departmen) {
                    $user->selected_departemen_id = $departmen->id;
                    $user->save();
                    return redirect()->route('dashboard.rule');
                } else {
                    // Jika tidak ada departemen yang ditemukan
                    return redirect()->route('select.dashboard')->withErrors(['departmen' => 'Departemen tidak ditemukan.']);
                }
            }
        }

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
        // ... validasi data

        $user = User::create([
            'npk' => $request->npk,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        // Simpan relasi user dengan departemen
        $user->departemens()->sync($request->departemen); // gunakan departemens()

        // Assign role ke user jika role ditemukan
        $defaultRoleName = 'guest';
        $role = Role::where('name', $defaultRoleName)->first();

        if ($role) {
            $user->assignRole($role);
        }

        Auth::login($user);

        // Tentukan departemen default dan simpan dalam session
        $departemen = $user->departemens->first(); // gunakan departemens()
        if ($departemen) {
            session(['active_departemen_id' => $departemen->id]);
        }

        Alert::success('Success', 'Registration successful! Please login.');
        return redirect()->route('login');
    }


    public function logout()
    {
        Auth::logout();
        session()->forget('active_departemen_id'); // Hapus departemen aktif dari session
        return redirect()->route('login')->with('success', 'you have successfully logged out!');
    }
}
