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
        $uniqueDepartemens = Departemen::select('code', 'nama_departemen')->distinct()->get();


        return view('audit.itemAudit', compact('itemAudit', 'audit', 'uniqueDepartemens'));
    }
    public function store_itemAudit(Request $request)
    {
        DocumentAudit::create([
            'nama_dokumen' => $request->input('nama_dokumen'),
            'audit_id' => $request->input('audit_id'),
        ]);
        Alert::success('Success', 'Document audit added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('masterdata.documentAudit');
    }
    public function update_itemAudit(Request $request, $id)
    {
        $documentAudit = DocumentAudit::findOrFail($id);
        $documentAudit->nama_dokumen = $request->nama_dokumen;
        $documentAudit->audit_id = $request->audit_id;
        $documentAudit->save();
        Alert::success('Success', 'Document Audit changed succesfully.');
        return redirect()->back();
    }
    public function delete_itemAudit($id)
    {
        $documentAudit = DocumentAudit::findOrFail($id);
        $documentAudit->delete();

        Alert::success('Success', 'Document Audit has been deleted successfully.');
        return redirect()->back();
    }
}
