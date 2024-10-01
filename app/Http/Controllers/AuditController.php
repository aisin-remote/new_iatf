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
        if ($user->hasRole('admin')) {
            // Admin dapat melihat semua data
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])->where('departemen_id', $departemenId)->get();
        } else {
            // Non-admin hanya dapat melihat audit terkait departemen mereka
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])
                ->where('departemen_id', $departemenId) // Filter berdasarkan departemen ID
                ->get();
        }

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
        // Validasi file yang diupload
        $request->validate([
            'attachments' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480', // Maksimal 20MB per file
        ]);

        // Temukan entitas AuditControl
        $auditControl = AuditControl::find($id);

        if ($auditControl) {
            // Proses upload file
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');

                if ($file->isValid()) {
                    // Generate nama file baru
                    $filename = time() . '-' . $file->getClientOriginalName();

                    // Tentukan path penyimpanan dalam storage
                    $destinationPath = storage_path('app/public/documentsAudit'); // Path ke folder documentsAudit dalam storage

                    // Membuat folder jika belum ada
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true); // Membuat folder dengan permission 0755
                    }

                    // Pindahkan file ke direktori yang ditentukan
                    $file->move($destinationPath, $filename);

                    // Simpan informasi dokumen di database
                    $auditControl->documentAudit()->create([
                        'audit_control_id' => $auditControl->id, // Menggunakan ID kontrol audit
                        'attachment' => 'documentsAudit/' . $filename, // Simpan path relatif
                    ]);

                    // Perbarui status menjadi 'uncompleted' jika ada dokumen baru
                    $auditControl->status = 'uncompleted';
                    $auditControl->save(); // Simpan perubahan status
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
    public function auditDetails()
    {
        if (auth()->user()->hasRole('admin')) {
            // Admin dapat melihat semua data, ambil data unik berdasarkan audit_id
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])
                ->select('item_audit_id') // Ganti dengan kolom yang Anda butuhkan
                ->distinct()
                ->get();
        } else {
            // Pengguna selain admin hanya melihat data sesuai departemen
            $departemenId = auth()->user()->departemen_id; // Ambil ID departemen pengguna
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])
                ->whereHas('departemen', function ($query) use ($departemenId) {
                    $query->where('id', $departemenId);
                })
                ->select('item_audit_id') // Ganti dengan kolom yang Anda butuhkan
                ->distinct()
                ->get();
        }

        $itemaudit = ItemAudit::all();

        // Ambil data departemen yang unik
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        // Mengirimkan data ke view
        return view('audit.auditcontrol', compact('AuditControls', 'itemaudit', 'uniqueDepartemens'));
    }
    public function approveDocumentAudit($id)
    {
        // Cari dokumen audit berdasarkan ID audit control
        $auditControl = AuditControl::findOrFail($id);

        // Pastikan dokumen audit ada
        if ($auditControl->documentAudit->count()) {
            // Loop untuk meng-update status dokumen jika perlu (jika ada status di documentAudit)
            foreach ($auditControl->documentAudit as $document) {
                // Misalkan jika Anda memiliki status di documentAudit juga, ubah di sini
                // Jika tidak perlu, abaikan bagian ini
                // $document->status = 'completed'; 
                // $document->save();
            }

            // Set status 'completed' pada audit control
            $auditControl->status = 'completed'; // Mengupdate status di audit_control
            $auditControl->save(); // Simpan perubahan status audit control

            // Set pesan sukses
            return redirect()->back()->with('success', 'Document(s) approved successfully.');
        }

        // Jika tidak ada dokumen audit ditemukan
        return redirect()->back()->with('error', 'No documents found to approve.');
    }

    // Fungsi untuk reject dokumen
    public function rejectDocumentAudit($id)
    {
        // Cari dokumen audit berdasarkan ID audit control
        $auditControl = AuditControl::findOrFail($id);

        // Pastikan dokumen audit ada
        if ($auditControl->documentAudit->count()) {
            foreach ($auditControl->documentAudit as $document) {
                // Set status rejected pada dokumen
                $document->status = 'uncompleted';
                $document->save();
            }

            // Set pesan sukses
            return redirect()->back()->with('success', 'Document(s) rejected successfully.');
        }

        // Jika tidak ada dokumen audit ditemukan
        return redirect()->back()->with('error', 'No documents found to reject.');
    }
}
