<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\AuditDepartemen;
use App\Models\Departemen;
use App\Models\DocumentAudit;
use App\Models\DocumentAuditControl;
use App\Models\ItemAudit;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AuditController extends Controller
{
    public function index_auditControl()
    {

        $AuditControls = AuditControl::with(['itemAudit.audit', 'departemen'])->get();

        $itemaudit = ItemAudit::with('audit')->get();

        // Ambil data departemen yang unik
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        // Mengirimkan data ke view
        return view('audit.auditcontrol', compact('AuditControls', 'itemaudit', 'uniqueDepartemens'));
    }
    // YourController.php

    public function uploadDocumentAudit(Request $request, $id)
    {
        // Validasi file yang diupload
        $request->validate([
            'attachments' => 'required|array', // Pastikan attachments adalah array
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:20480', // Maksimal 20MB per file
        ]);

        // Temukan entitas AuditControl
        $auditControl = AuditControl::find($id);
        dd($request->all());
        if ($auditControl) {
            // Proses upload file
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) { // Pastikan file valid
                        $filename = time() . '-' . $file->getClientOriginalName();
                        $file->storeAs('public/documentsAudit', $filename);

                        // Simpan informasi dokumen di database per baris
                        $auditControl->documentAudit()->create([
                            'audit_control_id' => $id,
                            'attachment' => 'documentsAudit/' . $filename,
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Documents uploaded successfully');
    }
}
