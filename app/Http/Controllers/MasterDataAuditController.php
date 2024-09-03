<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\AuditDepartemen;
use App\Models\Departemen;
use App\Models\DocumentAudit;
use App\Models\ItemAudit;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MasterDataAuditController extends Controller
{
    public function master_audit()
    {
        $audit = Audit::all();
        return view('audit.audit', compact('audit'));
    }
    public function store_audit(Request $request)
    {
        Audit::create([
            'nama' => $request->input('nama'),
            'tanggal_audit' => $request->input('tanggal_audit'),
            'reminder' => $request->input('reminder'),
            'duedate' => $request->input('duedate')
        ]);
        Alert::success('Success', 'Audit added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('masterdata.audit');
    }
    public function update_audit(Request $request, $id)
    {
        $audit = Audit::findOrFail($id);
        $audit->nama = $request->nama;
        $audit->tanggal_audit = $request->tanggal_audit;
        $audit->save();
        Alert::success('Success', 'Audit code changed succesfully.');
        return redirect()->back();
    }
    public function delete_audit($id)
    {
        $audit = Audit::findOrFail($id);
        $audit->delete();

        Alert::success('Success', 'Audit has been deleted successfully.');
        return redirect()->back();
    }
    public function master_itemAudit()
    {
        $itemAudit = ItemAudit::with('audit')->get();
        $audit = Audit::all();
        $uniqueDepartemens = Departemen::distinct()->get(['id', 'nama_departemen']); // Ambil ID dan nama departemen

        return view('audit.itemAudit', compact('itemAudit', 'audit', 'uniqueDepartemens'));
    }

    public function store_itemAudit(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'audit_id' => 'required|exists:audit,id', // Pastikan tabel 'audits' ada dan kolom 'id' benar
        ]);

        // Ambil data input untuk nama item dan audit_id
        $namaItem = $request->input('nama_item');
        $auditId = $request->input('audit_id');

        // Buat entri baru di tabel ItemAudit
        ItemAudit::create([
            'nama_item' => $namaItem,
            'audit_id' => $auditId,
        ]);

        // Kirim notifikasi sukses
        Alert::success('Success', 'Item audit added successfully.');

        // Redirect kembali ke halaman item audit
        return redirect()->route('masterdata.itemAudit');
    }
    public function update_itemAudit(Request $request, $id)
    {
        $documentAudit = ItemAudit::findOrFail($id);
        $documentAudit->nama_item = $request->nama_item;
        $documentAudit->audit_id = $request->audit_id;
        $documentAudit->save();
        Alert::success('Success', 'Document Audit changed succesfully.');
        return redirect()->back();
    }
    public function delete_itemAudit($id)
    {
        $documentAudit = ItemAudit::findOrFail($id);
        $documentAudit->delete();

        Alert::success('Success', 'Document Audit has been deleted successfully.');
        return redirect()->back();
    }
    public function master_auditcontrol(Request $request)
    {
        $auditDepartemens = AuditDepartemen::with(['itemAudits', 'departemen'])->get();
        $itemaudit = ItemAudit::with('audit')->get();
        $uniqueDepartemens = Departemen::distinct()->get(['nama_departemen']);
        return view('audit.masterauditcontrol', compact('auditDepartemens', 'itemaudit', 'uniqueDepartemens'));
    }
    public function store_auditControl(Request $request)
    {
        dd($request->input('departemen'));
        // Validasi input
        // $request->validate([
        //     'item_audit_id' => 'required|exists:item_audit,id', // Pastikan item_audit_id valid
        //     'departemen' => 'required|array|min:1', // Pastikan setidaknya satu departemen dipilih
        //     'departemen.*' => 'integer|exists:departemen,id' // Validasi setiap ID departemen
        // ]);

        // Ambil item_audit_id dari request
        $itemAuditId = $request->input('item_audit_id');

        // Ambil semua ID departemen yang dipilih
        $departemenIds = $request->input('departemen');

        // Iterasi melalui setiap departemen yang dipilih dan simpan ke database
        foreach ($departemenIds as $departemenId) {
            AuditDepartemen::create([
                'departemen_id' => $departemenId,
                'item_audit_id' => $itemAuditId,
            ]);
        }

        Alert::success('Success', 'Audit Control added successfully.');
        return redirect()->route('auditControl'); // Pastikan route ini ada
    }
}
