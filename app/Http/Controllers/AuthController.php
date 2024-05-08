<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        // Mengambil semua data user
        $users = User::all();

        // Mengirim data user ke tampilan 'auth.login'
        return view('auth.login', ['users' => $users]);
    }
    public function login_proses(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Validasi input berhasil, lanjutkan dengan validasi autentikasi
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            return redirect()->route('home');
        } else {
            return redirect()->route('login')->with('failed', 'Username or password is incorrect');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('logout')->with('success', 'you have successfully logged out!');
    }
}
