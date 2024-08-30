<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\Departemen;
use App\Models\DocumentAudit;
use App\Models\RuleCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class masterDataController extends Controller
{
    public function index()
    {
        $departemenCount = Departemen::count();
        $rulecodeCount = RuleCode::count();
        $roleCount = Role::count();

        return view('master data.index', compact('departemenCount', 'rulecodeCount', 'roleCount'));
    }
    public function index_departemen()
    {
        $departemen = Departemen::all();
        return view('master data.departemen', compact('departemen'));
    }
    public function store_departemen(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'nama_departemen' => 'required|string|max:255',
        ]);

        Departemen::create([
            'code' => $request->input('code'),
            'nama_departemen' => $request->input('nama_departemen'),
        ]);
        Alert::success('Success', 'Departemen added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('masterdata.departemen');
    }
    public function update_departemen(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'nama_departemen' => 'required|string|max:255',
        ]);
        $departemen = Departemen::findOrFail($id);
        $departemen->code = $request->code;
        $departemen->nama_departemen = $request->nama_departemen;
        $departemen->save();
        Alert::success('Success', 'Departemen changed succesfully.');
        return redirect()->back();
    }
    public function delete_departemen($id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->delete();

        Alert::success('Success', 'Departemen has been deleted successfully.');
        return redirect()->back();
    }
    public function index_prosescode()
    {
        $kode_proses = RuleCode::all();
        return view('master data.rulecode', compact('kode_proses'));
    }
    public function store_kodeproses(Request $request)
    {
        $request->validate([
            'kode_proses' => 'required|string|max:255',
            'nama_proses' => 'required|string|max:255',
        ]);

        RuleCode::create([
            'kode_proses' => $request->input('kode_proses'),
            'nama_proses' => $request->input('nama_proses'),
        ]);
        Alert::success('Success', 'Process code added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('masterdata.departemen');
    }
    public function update_kodeproses(Request $request, $id)
    {
        $request->validate([
            'kode_proses' => 'required|string|max:255',
            'nama_proses' => 'required|string|max:255',
        ]);
        $kode_proses = RuleCode::findOrFail($id);
        $kode_proses->kode_proses = $request->kode_proses;
        $kode_proses->nama_proses = $request->nama_proses;
        $kode_proses->save();
        Alert::success('Success', 'Process code changed succesfully.');
        return redirect()->back();
    }
    public function delete_kodeproses($id)
    {
        $kode_proses = RuleCode::findOrFail($id);
        $kode_proses->delete();

        Alert::success('Success', 'Process code has been deleted successfully.');
        return redirect()->back();
    }
    public function index_role()
    {
        $roles = Role::all();
        return view('master data.role', compact('roles'));
    }

    public function store_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
        ]);

        if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
        }

        Role::create($request->only('name', 'guard_name'));

        return redirect()->route('masterdata.role');
    }

    public function delete_role(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
