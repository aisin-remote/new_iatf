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
        // Cek apakah pengguna adalah admin
        if (auth()->user()->hasRole('admin')) {
            // Admin dapat melihat semua data
            $AuditControls = AuditControl::with(['itemAudit.audit', 'departemen'])->get();
        } else {
            // Pengguna selain admin hanya melihat data sesuai departemen
            $departemenId = auth()->user()->departemen_id; // Asumsi departemen_id menyimpan ID departemen pengguna
            $AuditControls = AuditControl::with(['itemAudit.audit', 'departemen'])
                ->whereHas('departemen', function ($query) use ($departemenId) {
                    $query->where('id', $departemenId);
                })->get();
        }

        $itemaudit = ItemAudit::with('audit')->get();

        // Ambil data departemen yang unik
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        // Mengirimkan data ke view
        return view('audit.auditcontrol', compact('AuditControls', 'itemaudit', 'uniqueDepartemens'));
    }


    public function uploadDocumentAudit(Request $request, $id)
    {
        // Validasi file yang diupload (bukan array)
        $request->validate([
            'attachments' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480', // Maksimal 20MB per file
        ]);

        // Temukan entitas AuditControl
        $auditControl = AuditControl::find($id);

        if ($auditControl) {
            // Proses upload file
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments'); // Mengambil file dari input (satu file)

                if ($file->isValid()) { // Pastikan file valid
                    $filename = time() . '-' . $file->getClientOriginalName();
                    $file->storeAs('public/documentsAudit', $filename);

                    // Simpan informasi dokumen di database
                    $auditControl->documentAudit()->create([
                        'audit_control_id' => $id,
                        'attachment' => 'documentsAudit/' . $filename,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Document uploaded successfully');
    }
    public function deleteDocumentAudit($id)
    {
        $DocumentAuditControls = DocumentAuditControl::findOrFail($id);
        $DocumentAuditControls->delete();

        Alert::success('Success', 'Document Audit Control has been deleted successfully.');
        return redirect()->back();
    }
}
