<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function index()
    {
        // Mengirim data user ke tampilan 'auth.login'
        return view('auth.login');
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
            // Regenerate the session to prevent fixation attacks
            $request->session()->regenerate();

            // Menampilkan SweetAlert modal
            return view('select-dashboard');
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
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        $validator = Validator::make($request->all(), [
            'npk' => 'required|string|max:255|unique:users',
            'departemen' => 'required|exists:departemen,id',
            'password' => 'required|string|min:6|confirmed',
        ], $message);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Default role name for guest
        $defaultRoleName = 'guest';

        // Find the guest role
        $role = Role::where('name', $defaultRoleName)->first();

        // Create the user
        $user = User::create([
            'npk' => $request->npk,
            'departemen_id' => $request->departemen,
            'password' => Hash::make($request->password),
        ]);

        // Assign the guest role to the user
        if ($role) {
            $user->assignRole($role);
        }

        // Login user after registration
        Auth::login($user);

        return redirect()->route('login')->with('success', 'Registration successful! Please Login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'you have successfully logged out!');
    }
}
