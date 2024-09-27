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
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])->get();
        } else {
            // Pengguna selain admin hanya melihat data sesuai departemen
            $departemenId = auth()->user()->departemen_id; // Ambil ID departemen pengguna
            $AuditControls = AuditControl::with(['itemAudit', 'audit', 'departemen'])
                ->whereHas('departemen', function ($query) use ($departemenId) {
                    $query->where('id', $departemenId);
                })->get();
        }

        // Mengelompokkan AuditControl berdasarkan audit_id dan menghitung statusnya
        $groupedAuditControls = $AuditControls->groupBy('audit_id')->map(function ($group) {
            // Cek apakah semua statusnya 'completed'
            $allCompleted = $group->every(function ($item) {
                return $item->status === 'completed';
            });

            return [
                'audit_id' => $group->first()->audit_id,
                'status' => $allCompleted ? 'completed' : 'uncompleted',
                'data' => $group, // Menyimpan data audit yang terkait
                'audit_name' => $group->first()->audit->nama, // Mengambil nama audit
                'start_audit' => $group->first()->audit->start_audit, // Tanggal mulai paling awal
                'end_audit' => $group->first()->audit->end_audit, // Mengambil tanggal audit
            ];
        });

        // Mengirimkan data ke view
        $itemaudit = ItemAudit::all();
        $uniqueDepartemens = Departemen::all()->unique('nama_departemen');

        return view('audit.auditcontrol', compact('groupedAuditControls', 'itemaudit', 'uniqueDepartemens'));
    }
    public function showAuditDetails($audit_id)
    {
        // Ambil data audit yang berhubungan dengan audit_id dan relasi yang diperlukan
        $AuditControls = AuditControl::with(['itemAudit', 'departemen', 'documentAudit'])
            ->where('audit_id', $audit_id)
            ->get();

        // Grup data berdasarkan item_audit_id
        $groupedAuditControls = $AuditControls->groupBy('item_audit_id');

        // Dapatkan ID departemen pengguna yang sedang login
        $userDepartemenIds = auth()->user()->departemen->pluck('id');

        // Hitung jumlah dokumen yang di-upload per departemen terkait dengan item_audit
        $uploadedItems = $groupedAuditControls->map(function ($group) use ($userDepartemenIds) {
            $departemenIds = $group->pluck('departemen_id')->unique(); // Ambil unique departemen_id

            // Cek apakah ada departemen yang cocok dengan user
            $isRelevant = $departemenIds->intersect($userDepartemenIds)->isNotEmpty();

            // Jika tidak relevan, kembalikan null
            if (!$isRelevant) {
                return null;
            }

            // Hitung jumlah dokumen yang telah di-upload untuk setiap departemen
            $uploaded = $departemenIds->filter(function ($departemenId) use ($group) {
                $auditForDept = $group->where('departemen_id', $departemenId)->first();

                // Cek apakah ada dokumen yang di-upload untuk departemen ini
                return $auditForDept && $auditForDept->documentAudit->isNotEmpty();
            })->count();

            $total = $departemenIds->count(); // Hitung total departemen terkait

            // Tentukan status berdasarkan uploaded/total
            $status = ($uploaded === $total) ? 'Completed' : 'Uncompleted';

            return [
                'uploaded' => $uploaded,
                'total' => $total,
                'status' => $status,
                'itemAudit' => $group->first()->itemAudit, // Ambil itemAudit untuk ditampilkan
                'audit_id' => $group->first()->audit_id, // Ambil audit_id untuk detail
            ];
        })->filter(); // Filter null values

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
