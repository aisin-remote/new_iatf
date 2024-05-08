<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('pages.user.list', compact('users'));
    }
    public function create()
    {
        $users = User::all();
        return view('pages.user.create', compact('users'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departemen' => 'required',
            'username' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator);

        $user = new User();
        $user->departemen = $request->departemen;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;

        $user->save();
        return redirect()->route('user');
    }
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'departemen' => 'required',
            'username' => 'required',
            'role' => 'required|in:admin,departemen', // Pastikan role hanya bisa 'admin' atau 'departemen'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Temukan pengguna berdasarkan ID
        $user = User::find($id);

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Update data pengguna
        $user->departemen = $request->departemen;
        $user->username = $request->username;
        $user->role = $request->role;

        // Simpan perubahan
        $user->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'User updated successfully.');
    }
    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('user')->with('success', 'User deleted successfully.');
        }

        return redirect()->route('user')->with('error', 'User not found.');
    }
}
