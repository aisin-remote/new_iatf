<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\Departemen;
use App\Models\DocumentAudit;
use App\Models\ItemAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class MasterDataAuditController extends Controller
{
    public function master_audit(Request $request)
    {
        // Initialize the query to fetch audits
        $query = Audit::query();

        // Check for reminder date range filtering
        if (!empty($request->reminder) || !empty($request->duedate)) {
            if ($request->has('reminder') && $request->has('duedate')) {
                $query->whereBetween('reminder', [$request->reminder, $request->duedate]);
            } elseif ($request->has('reminder')) {
                $query->where('reminder', '>=', $request->reminder);
            } elseif ($request->has('duedate')) {
                $query->where('reminder', '<=', $request->duedate);
            }
        }

        // Check if either audit dates are filled
        if (!empty($request->start_audit) || !empty($request->end_audit)) {
            if ($request->has('start_audit') && $request->has('end_audit')) {
                $query->whereBetween('start_audit', [$request->start_audit, $request->end_audit]);
            } elseif ($request->has('start_audit')) {
                $query->where('start_audit', '>=', $request->start_audit);
            } elseif ($request->has('end_audit')) {
                $query->where('start_audit', '<=', $request->end_audit);
            }
        }

        // Retrieve the filtered audits ordered by updated_at
        $audit = $query->orderByDesc('updated_at')->get();

        // Return the view with the filtered audits
        return view('audit.audit', [
            'audit' => $audit,
            'reminder' => $request->reminder,
            'duedate' => $request->duedate,
            'start_audit' => $request->start_audit,
            'end_audit' => $request->end_audit,
        ]);
    }

    public function store_audit(Request $request)
    {
        Audit::create([
            'nama' => $request->input('nama'),
            'reminder' => $request->input('reminder'),
            'duedate' => $request->input('duedate'),
            'start_audit' => $request->input('start_audit'),
            'end_audit' => $request->input('end_audit'),
        ]);

        Alert::success('Success', 'Audit added successfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('masterdata.audit');
    }
    public function update_audit(Request $request, $id)
    {
        $audit = Audit::findOrFail($id);

        // Perbarui data audit
        $audit->update([
            'nama' => $request->input('nama'),
            'reminder' => $request->input('reminder'),
            'duedate' => $request->input('duedate'),
            'start_audit' => $request->input('start_audit'),
            'end_audit' => $request->input('end_audit'),
        ]);
        Alert::success('Success', 'Audit updated successfully.');

        // Redirect ke halaman lain atau tetap di halaman form
        return redirect()->route('masterdata.audit');
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
        $itemAudit = ItemAudit::orderByDesc('updated_at')
            ->get();
        $audit = Audit::all();


        return view('audit.itemAudit', compact('itemAudit', 'audit'));
    }

    public function store_itemAudit(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'requirement' => 'nullable|string',
            'example_requirement' => 'nullable|file|mimes:pdf,xlsx,docx|max:10240'
            // Pastikan tabel 'audits' ada dan kolom 'id' benar
        ]);

        // Ambil data input untuk nama item dan audit_id
        $namaItem = $request->input('nama_item');
        $requirement = $request->input('requirement');
        $filePath = null;
        if ($request->hasFile('example_requirement')) {
            // Simpan file di folder storage dan ambil path-nya
            $filePath = $request->file('example_requirement')->store('example_files', 'public');
        }

        // Buat entri baru di tabel ItemAudit
        ItemAudit::create([
            'nama_item' => $namaItem,
            'requirement' => $requirement,
            'example_requirement' => $filePath,
        ]);

        // Kirim notifikasi sukses
        Alert::success('Success', 'Item audit added successfully.');

        // Redirect kembali ke halaman item audit
        return redirect()->route('masterdata.itemAudit');
    }
    public function update_itemAudit(Request $request, $id)
    {
        $documentAudit = ItemAudit::findOrFail($id);
        $documentAudit->nama_item = $request->nama_item;
        $documentAudit->requirement = $request->requirement;
        if ($request->hasFile('example_requirement')) {
            // Jika ada file baru, hapus file lama jika ada
            if ($documentAudit->example_requirement) {
                Storage::disk('public')->delete($documentAudit->example_requirement);
            }

            // Simpan file baru dan update path
            $filePath = $request->file('example_requirement')->store('example_files', 'public');
            $documentAudit->example_requirement = $filePath;
        }

        $documentAudit->save();
        Alert::success('Success', 'Document Audit changed succesfully.');
        return redirect()->back();
    }
    public function delete_itemAudit($id)
    {
        $documentAudit = ItemAudit::findOrFail($id);
        $documentAudit->delete();

        Alert::success('Success', 'Document Audit has been deleted successfully.');
        return redirect()->back();
    }
    public function master_auditcontrol(Request $request)
    {
        // Start the query with AuditControl
        $query = AuditControl::with(['itemAudit', 'audit', 'departemen']);

        // Apply filters if they are set in the request
        if ($request->has('audit_id') && !empty($request->audit_id)) {
            $query->where('audit_id', $request->audit_id);
        }

        if ($request->has('item_audit_id') && !empty($request->item_audit_id)) {
            $query->where('item_audit_id', $request->item_audit_id);
        }

        if ($request->has('departemen') && !empty($request->departemen)) {
            $query->where('departemen_id', $request->departemen);
        }

        // Get the filtered results
        $AuditControls = $query->orderBy('updated_at', 'desc')->get();

        // Fetch data for dropdowns
        $itemaudit = ItemAudit::all();
        $audit = Audit::all();
        $uniqueDepartemens = Departemen::where('nama_departemen', '!=', 'Aisin Indonesia')->get();

        // Send data to the view
        return view('audit.masterauditcontrol')->with([
            'AuditControls' => $AuditControls,
            'itemaudit' => $itemaudit,
            'audit' => $audit,
            'uniqueDepartemens' => $uniqueDepartemens,
            // Include the current filter values in case you want to reset them
            'currentFilters' => [
                'audit_id' => $request->audit_id,
                'item_audit_id' => $request->item_audit_id,
                'departemen' => $request->departemen,
            ]
        ]);
    }

    public function store_auditControl(Request $request)
    {
        // Ambil item_audit_id dari request
        $itemAuditIds = $request->input('item_audit_id', []); // Pastikan ini berupa array
        $departemenIds = $request->input('departemen', []); // Ambil semua ID departemen yang dipilih
        $auditId = $request->input('audit_id');

        // Validasi input untuk memastikan semua item_audit_id dan departemen_ids valid
        $request->validate([
            'item_audit_id' => 'required|array', // Memastikan ini array
            'item_audit_id.*' => 'exists:item_audit,id', // Memastikan setiap item audit ada di database
            'departemen' => 'required|array', // Memastikan ini array
            'departemen.*' => 'exists:departemen,id', // Memastikan setiap departemen ada di database
            'audit_id' => 'required|exists:audit,id' // Memastikan audit_id valid
        ]);

        // Iterasi melalui setiap departemen yang dipilih
        foreach ($departemenIds as $departemenId) {
            // Untuk setiap departemen, iterasi melalui setiap item audit yang dipilih
            foreach ($itemAuditIds as $itemAuditId) {
                // Simpan ke database
                AuditControl::create([
                    'departemen_id' => $departemenId,
                    'item_audit_id' => $itemAuditId,
                    'audit_id' => $auditId,
                    'status' => 'uncomplete',
                    'comment' => 'Please upload your document!' // Atur status default
                ]);
            }
        }

        // Menampilkan notifikasi sukses
        Alert::success('Success', 'Audit Control added successfully.');
        return redirect()->route('masterdata.auditControl');
    }
    public function update_auditcontrol(Request $request, $id)
    {
        $AuditControls = AuditControl::findOrFail($id);
        $AuditControls->departemen_id = $request->departemen;
        $AuditControls->item_audit_id = $request->item_audit_id;
        $AuditControls->status = 'uncomplete';
        $AuditControls->save();
        Alert::success('Success', 'Document Audit changed succesfully.');
        return redirect()->back();
    }
    public function delete_auditcontrol($id)
    {
        $AuditControls = AuditControl::findOrFail($id);
        $AuditControls->delete();

        Alert::success('Success', 'Document Audit has been deleted successfully.');
        return redirect()->back();
    }
}
