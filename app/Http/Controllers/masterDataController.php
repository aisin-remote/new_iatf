<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\RuleCode;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class masterDataController extends Controller
{
    public function index()
    {
        return view('master data.index');
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
}
