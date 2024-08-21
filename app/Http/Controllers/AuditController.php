<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\DocumentAudit;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AuditController extends Controller
{
    public function index_auditControl()
    {
        $auditControls = AuditControl::with(['documentAudit', 'audit'])->get();
        $audit = Audit::all();
        $documentAudits = DocumentAudit::all();
        return view('auditControl', compact('auditControls', 'audit', 'documentAudits'));
    }
    public function store_auditControl(Request $request)
    {
        AuditControl::create([
            'dokumenaudit_id' => $request->input('documentaudit_id'),
            'audit_id' => $request->input('audit_id'),
            'reminder' => $request->input('reminder'),
            'duedate' => $request->input('duedate'),
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
        $auditControl->reminder = $request->input('reminder');
        $auditControl->duedate = $request->input('duedate');
        $auditControl->attachment = $request->input('attachment');
        $auditControl->save();
        Alert::success('Success', 'Audit control changed succesfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('auditControl');
    }
}
