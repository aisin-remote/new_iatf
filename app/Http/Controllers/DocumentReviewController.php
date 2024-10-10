<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\DocumentReview;
use Illuminate\Http\Request;

class DocumentReviewController extends Controller
{
    public function list()
    {
        $departments = Departemen::orderBy('nama_departemen', 'ASC')->get();

        $document_controls = DocumentReview::orderBy('name', 'ASC')->get();

        return view('document_control.list', compact('departments', 'document_controls'));
    }

    public function list_ajax(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $data = DocumentReview::orderBy('name', 'ASC');
        } else {
            $data = DocumentReview::select('document_controls.*')
                ->join('departemen', 'document_controls.department', '=', 'departemen.nama_departemen')
                ->where('departemen.id', $user->departemen_id)
                ->orderBy('document_controls.name', 'ASC');
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
                $document_control = DocumentReview::create([
                    'name' => $request->name,
                    'department' => $department,
                    'obsolete' => $request->obsolete,
                    'set_reminder' => $request->set_reminder,
                    'comment' => $request->comment,
                    'status' => 'Unuploaded',
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

        $document_control = DocumentReview::findOrFail($id);
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

        $document_control = DocumentReview::findOrFail($id);
        $document_control->delete();

        return "Delete Successfully";
    }

    public function approve(Request $request)
    {
        $id = $request->id;

        $document_control = DocumentReview::findOrFail($id);
        $document_control->update([
            'status' => 'Approved',
            'comment' => 'Document has been approved!'
        ]);

        return "Approve Successfully";
    }

    public function reject(Request $request)
    {
        $id = $request->id;

        $document_control = DocumentReview::findOrFail($id);
        $document_control->update([
            'status' => 'Rejected',
            'comment' => $request->comment_reject,
        ]);

        return "Reject Successfully";
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:20480',
        ]);

        $document_control = DocumentReview::findOrFail($request->id);

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
        $document_control = DocumentReview::findOrFail($request->id);

        if ($document_control->file) {
            $fileUrl = asset('storage/document_control/' . $document_control->file);
            return response()->json(['file_url' => $fileUrl]);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}
