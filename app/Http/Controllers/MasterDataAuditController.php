<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
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
            'requirement' => 'nullable|string',
            'example_requirement' => 'nullable|file|mimes:pdf,xlsx,docx|max:10240'
            // Pastikan tabel 'audits' ada dan kolom 'id' benar
        ]);

        // Ambil data input untuk nama item dan audit_id
        $namaItem = $request->input('nama_item');
        $requirement = $request->input('requirement');
        $filePath = null;
        if ($request->hasFile('example_requirement')) {
            // Simpan file di folder storage dan ambil path-nya
            $filePath = $request->file('example_requirement')->store('example_files', 'public');
        }

        // Buat entri baru di tabel ItemAudit
        ItemAudit::create([
            'nama_item' => $namaItem,
            'requirement' => $requirement,
            'example_requirement' => $filePath,
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
        $AuditControls = AuditControl::with(['itemAudit.audit', 'departemen'])->get();

        // Ambil data item audit untuk digunakan dalam dropdown di modal
        $itemaudit = ItemAudit::all();
        $audit = Audit::all();

        // Ambil data departemen yang unik
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        // Mengirimkan data ke view
        return view('audit.masterauditcontrol', compact('AuditControls', 'itemaudit', 'uniqueDepartemens', 'audit'));
    }
    public function store_auditControl(Request $request)
    {
        // Ambil item_audit_id dari request
        $itemAuditId = $request->input('item_audit_id');

        // Ambil semua ID departemen yang dipilih
        $departemenIds = $request->input('departemen');

        // Iterasi melalui setiap departemen yang dipilih dan simpan ke database
        foreach ($departemenIds as $departemenId) {
            AuditControl::create([
                'departemen_id' => $departemenId,
                'item_audit_id' => $itemAuditId,
            ]);
        }

        Alert::success('Success', 'Audit Control added successfully.');
        return redirect()->route('masterdata.auditControl'); // Pastikan route ini ada
    }
    public function update_auditcontrol(Request $request, $id)
    {
        $AuditControls = AuditControl::findOrFail($id);
        $AuditControls->departemen_id = $request->departemen;
        $AuditControls->item_audit_id = $request->item_audit_id;
        $AuditControls->save();
        Alert::success('Success', 'Document Audit changed succesfully.');
        return redirect()->back();
    }
    public function delete_auditcontrol($id)
    {
        $AuditControls = AuditControl::findOrFail($id);
        $AuditControls->delete();

        Alert::success('Success', 'Document Audit has been deleted successfully.');
        return redirect()->back();
    }
}
