<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\DocumentAudit;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AuditController extends Controller
{
    
    public function master_audit()
    {
        $audit = Audit::all();
        return view('master data.audit', compact('audit'));
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
    public function index_documentAudit()
    {
        $documentaudit = DocumentAudit::with('audit')->get();
        $audit = Audit::all();
        return view('master data.documentAudit', compact('documentaudit', 'audit'));
    }
    public function store_documentAudit(Request $request)
    {
        DocumentAudit::create([
            'nama_dokumen' => $request->input('nama_dokumen'),
            'audit_id' => $request->input('audit_id'),
        ]);
        Alert::success('Success', 'Document audit added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('masterdata.documentAudit');
    }
    public function update_documentAudit(Request $request, $id)
    {
        $documentAudit = DocumentAudit::findOrFail($id);
        $documentAudit->nama_dokumen = $request->nama_dokumen;
        $documentAudit->audit_id = $request->audit_id;
        $documentAudit->save();
        Alert::success('Success', 'Document Audit changed succesfully.');
        return redirect()->back();
    }
    public function delete_documentAudit($id)
    {
        $documentAudit = DocumentAudit::findOrFail($id);
        $documentAudit->delete();

        Alert::success('Success', 'Document Audit has been deleted successfully.');
        return redirect()->back();
    }

    public function index_auditControl(Request $request)
    {
        $query = AuditControl::with(['document_audit', 'audit']);

        // Filter berdasarkan dokumenaudit_id
        if ($request->has('dokumenaudit_id') && $request->dokumenaudit_id != '') {
            $query->where('dokumenaudit_id', $request->dokumenaudit_id);
        }

        // Filter berdasarkan audit_id
        if ($request->has('audit_id') && $request->audit_id != '') {
            $query->where('audit_id', $request->audit_id);
        }

        // Filter berdasarkan reminder date
        if ($request->has('reminder') && $request->reminder != '') {
            $query->whereDate('reminder', $request->reminder);
        }

        // Filter berdasarkan due date
        if ($request->has('duedate') && $request->duedate != '') {
            $query->whereDate('duedate', $request->duedate);
        }

        $auditControls = $query->get();
        $audit = Audit::all();
        $documentAudits = DocumentAudit::all();

        return view('auditControl', compact('auditControls', 'audit', 'documentAudits'));
    }


    public function store_auditControl(Request $request)
    {
        AuditControl::create([
            'dokumenaudit_id' => $request->input('documentaudit_id'),
            'audit_id' => $request->input('audit_id'),
            'attachment' => $request->input('attachment'),
        ]);
        Alert::success('Success', 'Audit Control added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('auditControl');
    }
    public function update_auditControl(Request $request, $id)
    {
        $auditControl = AuditControl::findOrFail($id);
        $auditControl->dokumenaudit_id = $request->input('documentaudit_id');
        $auditControl->audit_id = $request->input('audit_id');
        $auditControl->attachment = $request->input('attachment');
        $auditControl->save();
        Alert::success('Success', 'Audit control changed succesfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('auditControl');
    }
}
