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
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AuditController extends Controller
{
    public function index_auditControl($departemenId)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Jika admin, tampilkan semua audit. Jika bukan admin, tampilkan hanya audit dari departemen terkait
        $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])
            ->where('departemen_id', $departemenId)
            ->get();

        // Group AuditControls by audit_id and count their status
        $groupedAuditControls = $AuditControls->groupBy('audit_id')->map(function ($group) {
            // Check if all statuses are 'completed'
            $allCompleted = $group->every(function ($item) {
                return $item->status === 'completed';
            });

            return [
                'audit_id' => $group->first()->audit_id,
                'status' => $allCompleted ? 'completed' : 'uncompleted',
                'data' => $group, // Store related audit data
                'audit_name' => $group->first()->audit->nama, // Get audit name
                'start_audit' => $group->first()->audit->start_audit, // Get earliest start date
                'end_audit' => $group->first()->audit->end_audit, // Get audit date
            ];
        });

        // Ambil semua item audit
        $itemaudit = ItemAudit::all();

        // Ambil semua departemen untuk sidebar
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        // Lempar data ke blade
        return view('audit.auditcontrol', compact('groupedAuditControls', 'itemaudit', 'uniqueDepartemens', 'departemenId'));
    }

    public function showAuditDetails($audit_id, $departemen_id)
    {
        // Ambil audit terkait dengan audit_id dan departemen_id
        $audit = Audit::findOrFail($audit_id);
        $departemen = Departemen::findOrFail($departemen_id);

        // Ambil AuditControl berdasarkan audit_id dan departemen_id
        $auditControls = AuditControl::with(['itemAudit', 'departemen', 'audit'])
            ->where('audit_id', $audit_id)
            ->where('departemen_id', $departemen_id)
            ->get();

        // Kirim data audit dan item audit ke view
        return view('audit.auditdetails', compact('audit', 'departemen', 'auditControls'));
    }

    public function uploadDocumentAudit(Request $request, $id)
    {
        // Validasi file yang diupload, setiap file diperiksa
        $request->validate([
            'attachments.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480', // Maksimal 20MB per file
        ]);

        // Temukan entitas AuditControl berdasarkan id
        $auditControl = AuditControl::findOrFail($id);

        // Proses upload file jika file ada
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    // Generate nama file baru
                    $filename = time() . '-' . $file->getClientOriginalName();

                    // Pindahkan file ke direktori yang ditentukan di storage Laravel
                    $file->storeAs('public/documentsAudit', $filename); // Simpan di storage/app/public/documentsAudit

                    // Simpan informasi dokumen di database
                    $auditControl->documentAudit()->create([
                        'attachment' => 'documentsAudit/' . $filename, // Simpan path relatif
                    ]);
                }
            }

            // Perbarui status menjadi 'submitted' jika ada dokumen baru yang di-upload
            $auditControl->status = 'submitted';
            $auditControl->comment = 'The document has been submitted';
            $auditControl->save(); // Simpan perubahan status
        }

        return redirect()->back()->with('success', 'Documents uploaded successfully');
    }

    public function deleteDocumentAudit($id)
    {
        $DocumentAuditControls = DocumentAuditControl::findOrFail($id);
        $DocumentAuditControls->delete();

        Alert::success('Success', 'Document Audit Control has been deleted successfully.');
        return redirect()->back();
    }

    public function approveItemAudit(Request $request, $audit_control_id)
    {
        // Cari item audit berdasarkan ID
        $auditControl = AuditControl::findOrFail($audit_control_id);

        // Update status AuditControl menjadi 'approved'
        $auditControl->status = 'completed';
        $auditControl->comment = 'The document has been approved';
        $auditControl->save();

        return redirect()->back()->with('success', 'Item audit approved successfully');
    }

    // Fungsi untuk reject dokumen
    public function rejectItemAudit(Request $request, $audit_control_id)
    {
        $auditControl = AuditControl::findOrFail($audit_control_id);

        // Update status AuditControl menjadi 'approved'
        $auditControl->status = 'uncomplete';
        $auditControl->comment = $request->comment;
        $auditControl->save();

        return redirect()->back()->with('success', 'Item audit rejected successfully');
    }
}
