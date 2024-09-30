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
    public function index_auditControl()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if the user is an admin
        if ($user->hasRole('admin')) {
            // Admin can view all data
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])->get();
        } else {
            // Non-admin users can only view data related to their department
            $departemenId = $user->departemen_id; // Get user's department ID
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])
                ->whereHas('departemen', function ($query) use ($departemenId) {
                    $query->where('id', $departemenId);
                })->get();
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

        // Send data to the view
        $itemaudit = ItemAudit::all();
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        return view('audit.auditcontrol', compact('groupedAuditControls', 'itemaudit', 'uniqueDepartemens'));
    }

    public function showAuditDetails($audit_id)
    {
        // Get the ID of the department of the logged-in user
        $userDepartemenId = Auth::user()->departemen_id;

        // Get the roles of the logged-in user
        $userRoles = auth()->user()->getRoleNames();

        // Prepare the AuditControls query
        $AuditControlsQuery = AuditControl::with(['itemAudit', 'departemen', 'documentAudit'])
            ->where('audit_id', $audit_id);

        // If the user is not an admin, filter by their department
        if (!$userRoles->contains('admin')) {
            $AuditControlsQuery->where('departemen_id', $userDepartemenId);
        }

        // Execute the query
        $AuditControls = $AuditControlsQuery->get();
        // dd($audit_id);

        // Group data by item_audit_id
        $groupedAuditControls = $AuditControls->groupBy('item_audit_id');

        $uploadedItems = $groupedAuditControls->map(function ($group) use ($userDepartemenId, $userRoles) {
            // Ambil item pertama sebagai objek AuditControl
            $firstAuditControl = $group->first();
            $departemenIds = $group->pluck('departemen_id')->unique();

            // Periksa relevansi audit item untuk departemen pengguna
            if ($userRoles->contains('admin') || $departemenIds->contains($userDepartemenId)) {
                $uploaded = $group->filter(function ($auditControl) use ($userDepartemenId) {
                    return $auditControl->departemen_id === $userDepartemenId &&
                        ($auditControl->documentAudit && $auditControl->documentAudit->isNotEmpty());
                })->count();

                $total = $departemenIds->count();

                // Menentukan status berdasarkan jumlah yang diupload dan total
                $status = ($uploaded === $total) ? 'Completed' : 'Uncompleted';

                return (object)[ // Mengembalikan sebagai objek
                    'uploaded' => $uploaded,
                    'total' => $total,
                    'status' => $status,
                    'itemAudit' => $firstAuditControl->itemAudit,
                    'audit_id' => $firstAuditControl->audit_id,
                    'documents' => $firstAuditControl->documentAudit, // Ini tetap objek
                ];
            }

            return null; // Jika tidak relevan, kembalikan null
        })->filter(); // Filter null    

        return view('audit.detailauditcontrol', compact('uploadedItems', 'AuditControls'));
    }

    public function showItemDetails($audit_id, $item_audit_id)
    {
        // Ambil data audit control berdasarkan audit_id dan item_audit_id
        $AuditControls = AuditControl::with(['itemAudit', 'departemen'])
            ->where('audit_id', $audit_id)
            ->where('item_audit_id', $item_audit_id)
            ->get();

        // Pastikan auditControl ditemukan
        if ($AuditControls->isEmpty()) {
            return redirect()->back()->with('error', 'Audit Control tidak ditemukan.');
        }

        return view('audit.detaildocumentaudit', compact('AuditControls'));
    }

    public function uploadDocumentAudit(Request $request, $id)
    {
        // dd($id);
        // Validasi file yang diupload
        // $request->validate([
        //     'attachments' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480', // Maksimal 20MB per file
        // ]);

        // Temukan entitas AuditControl
        $auditControl = AuditControl::find($id);


        if ($auditControl) {
            // Proses upload file
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');

                if ($file->isValid()) {
                    $filename = time() . '-' . $file->getClientOriginalName();
                    $file->storeAs('public/documentsAudit', $filename);

                    // Simpan informasi dokumen di database
                    $auditControl->documentAudit()->create([
                        'audit_control_id' => $id,
                        'attachment' => 'documentsAudit/' . $filename,
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
    public function approveDocument($id)
    {
        // Cari dokumen audit berdasarkan ID audit control
        $auditControl = AuditControl::findOrFail($id);

        // Pastikan dokumen audit ada
        if ($auditControl->documentAudit->count()) {
            foreach ($auditControl->documentAudit as $document) {
                // Set status approved pada dokumen
                $document->status = 'completed';
                $document->save();
            }

            // Set pesan sukses
            return redirect()->back()->with('success', 'Document(s) approved successfully.');
        }

        // Jika tidak ada dokumen audit ditemukan
        return redirect()->back()->with('error', 'No documents found to approve.');
    }

    // Fungsi untuk reject dokumen
    public function rejectDocument($id)
    {
        // Cari dokumen audit berdasarkan ID audit control
        $auditControl = AuditControl::findOrFail($id);

        // Pastikan dokumen audit ada
        if ($auditControl->documentAudit->count()) {
            foreach ($auditControl->documentAudit as $document) {
                // Set status rejected pada dokumen
                $document->status = 'rejected';
                $document->save();
            }

            // Set pesan sukses
            return redirect()->back()->with('success', 'Document(s) rejected successfully.');
        }

        // Jika tidak ada dokumen audit ditemukan
        return redirect()->back()->with('error', 'No documents found to reject.');
    }
}
