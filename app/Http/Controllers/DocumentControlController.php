<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DocumentControl;
use App\Models\Departemen;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DataTables;
use Auth;

class DocumentControlController extends Controller
{
    public function fetchDocumentControls()
    {
        // Mengambil data dari tabel document control
        $documents = DocumentControl::where('status', 'Uncomplete')
        ->where('department','!','Aisin Indonesia')
        ->orderBy('department','ASC')
        ->get(); // Anda bisa menyesuaikan query jika diperlukan
        // Mengembalikan data dalam format JSON
        // dd($documents);
        return response()->json($documents);
    }
    public function list(Request $request)
    {
        // Ambil semua departemen untuk dropdown
        $departments = Departemen::orderBy('nama_departemen', 'ASC')->get();

        // Inisialisasi query untuk DocumentControl
        $document_controls = DocumentControl::query();

        // Tambahkan filter untuk departemen jika ada
        if ($request->has('department') && $request->department != '') {
            $document_controls->where('department', $request->department);
        }

        // Tambahkan filter untuk status jika ada
        if ($request->has('status') && $request->status != '') {
            $document_controls->where('status', $request->status);
        }

        // Ambil data dokumen sesuai filter yang diterapkan
        $document_controls = $document_controls->orderBy('name', 'ASC')->get();

        return view('document_control.list', compact('departments', 'document_controls'));
    }


    public function list_ajax(Request $request)
    {
        $user = auth()->user();
        $department = $request->input('department');
        $status = $request->input('status');

        if ($user->hasRole('admin')) {
            // Admin dapat melihat semua data
            $data = DocumentControl::orderBy('name', 'ASC');
        } else {
            // User biasa hanya bisa melihat data yang sesuai dengan departemennya
            $data = DocumentControl::select('document_controls.*')
                ->join('departemen', 'document_controls.department', '=', 'departemen.nama_departemen')
                ->where('departemen.id', $user->departemen_id)
                ->orderBy('document_controls.name', 'ASC');
        }

        // Terapkan filter jika ada
        if (!empty($department)) {
            $data->where('document_controls.department', $department);
        }

        if (!empty($status)) {
            $data->where('document_controls.status', $status);
        }

        return DataTables::eloquent($data)->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'department' => 'required|array',
            'obsolete' => 'required',
            'set_reminder' => 'required',
            'comment' => 'required',
        ]);

        try {
            foreach ($request->department as $department) {
                $document_control = DocumentControl::create([
                    'name' => $request->name,
                    'department' => $department,
                    'obsolete' => $request->obsolete,
                    'set_reminder' => $request->set_reminder,
                    'comment' => $request->comment,
                    'status' => 'Uncomplete',
                ]);

                $document_control->save();
            }

            return response()->json(['message' => 'Create Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request)
    {
        $id = $request->id;

        $document_control = DocumentControl::findOrFail($id);
        $document_control->update([
            'name' => $request->name,
            'department' => $request->department,
            'obsolete' => $request->obsolete,
            'set_reminder' => $request->set_reminder,
            'comment' => $request->comment,
        ]);

        return "Update Successfully";
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $document_control = DocumentControl::findOrFail($id);
        $document_control->delete();

        return "Delete Successfully";
    }

    public function approve(Request $request)
    {
        $id = $request->id;

        $document_control = DocumentControl::findOrFail($id);
        $document_control->update([
            'status' => 'Completed',
            'comment' => 'Document has been approved!'
        ]);

        return "Approve Successfully";
    }

    public function reject(Request $request)
    {
        $id = $request->id;

        $document_control = DocumentControl::findOrFail($id);
        $document_control->update([
            'status' => 'Rejected',
            'file' => null,
            'comment' => $request->comment_reject,
        ]);

        return "Reject Successfully";
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        ], [
            'file.required' => 'File is required.',
            'file.mimes' => 'Only PDF, Word, and Excel files are allowed.',
            'file.max' => 'File size should not exceed 2MB.',
        ]);

        $document_control = DocumentControl::findOrFail($request->id);

        if ($request->hasFile('file')) {
            if ($document_control->file) {
                Storage::delete('document_control/' . $document_control->file);
            }

            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $request->file->storeAs('document_control', $fileName, 'public');

            $document_control->update([
                'file' => $fileName,
                'status' => 'Submitted',
            ]);
        }

        return response()->json('File uploaded successfully');
    }

    public function file(Request $request)
    {
        $document_control = DocumentControl::findOrFail($request->id);

        if ($document_control->file) {
            $fileUrl = asset('storage/document_control/' . $document_control->file);
            return response()->json(['file_url' => $fileUrl]);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}
