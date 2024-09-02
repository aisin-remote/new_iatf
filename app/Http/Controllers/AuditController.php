<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\DocumentAudit;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AuditController extends Controller
{
    public function master_auditControl(Request $request)
    {
        $query = AuditControl::with(['item_audit', 'audit']);

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

        return view('audit.AuditControl', compact('auditControls', 'audit', 'documentAudits'));
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
