<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $message = [
            'npk.required' => 'NPK tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ];

        // Validate the request data with custom messages
        $validator = Validator::make($request->all(), [
            'npk' => 'required|string',
            'password' => 'required|string',
        ], $message);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->route('login')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Retrieve only the 'npk' and 'password' from the request
        $credentials = $request->only('npk', 'password');

        // Attempt to authenticate the user with the provided credentials
        if (Auth::attempt($credentials)) {
            // Menampilkan SweetAlert modal
            return redirect()->route('select.dashboard');
        }

        // If authentication fails, redirect back to the login page with an error message
        return redirect()->route('login')->with('error', 'NPK atau password salah.');
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
            'name.required' => 'Nama tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        $validator = Validator::make($request->all(), [
            'npk' => 'required|string|max:255|unique:users',
            'departemen' => 'required|exists:departemen,id',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
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
