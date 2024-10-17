<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\DocumentReview;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



class DocumentReviewController extends Controller
{
    public function fetchDocumentReviews()
    {
        // Mengambil data dari tabel document control
        $documents = DocumentReview::orderBy('department','ASC')
        ->where('status', 'Uncomplete')->get(); // Anda bisa menyesuaikan query jika diperlukan
        // Mengembalikan data dalam format JSON
        // dd($documents);
        return response()->json($documents);
    }
    public function list()
    {
        $departments = Departemen::orderBy('nama_departemen', 'ASC')->get();

        $document_reviews = DocumentReview::orderBy('name', 'ASC')->get();

        return view('document_review.list', compact('departments', 'document_reviews'));
    }

    public function list_ajax(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $data = DocumentReview::orderBy('name', 'ASC');
        } else {
            $data = DocumentReview::select('document_reviews.*')
                ->join('departemen', 'document_reviews.department', '=', 'departemen.nama_departemen')
                ->where('departemen.id', $user->departemen_id)
                ->orderBy('document_reviews.name', 'ASC');
        }

        return DataTables::eloquent($data)->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'department' => 'required|array',
            'review' => 'required',
            'set_reminder' => 'required',
            'comment' => 'required',
        ]);

        try {
            foreach ($request->department as $department) {
                $document_review = DocumentReview::create([
                    'name' => $request->name,
                    'department' => $department,
                    'review' => $request->review,
                    'set_reminder' => $request->set_reminder,
                    'comment' => $request->comment,
                    'status' => 'Uncomplete',
                ]);

                $document_review->save();
            }

            return response()->json(['message' => 'Create Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request)
    {
        $id = $request->id;

        $document_review = DocumentReview::findOrFail($id);
        $document_review->update([
            'name' => $request->name,
            'department' => $request->department,
            'review' => $request->review,
            'set_reminder' => $request->set_reminder,
            'comment' => $request->comment,
        ]);

        return "Update Successfully";
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $document_review = DocumentReview::findOrFail($id);
        $document_review->delete();

        return "Delete Successfully";
    }

    public function approve(Request $request)
    {
        $id = $request->id;

        $document_review = DocumentReview::findOrFail($id);
        $document_review->update([
            'status' => 'Completed',
            'comment' => 'Document has been approved!'
        ]);

        return "Approve Successfully";
    }

    public function reject(Request $request)
    {
        $id = $request->id;

        $document_review = DocumentReview::findOrFail($id);
        $document_review->update([
            'status' => 'Rejected',
            'comment' => $request->comment_reject,
            'file' => null,
        ]);

        return "Reject Successfully";
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        ]);

        $document_review = DocumentReview::findOrFail($request->id);

        if ($request->hasFile('file')) {
            if ($document_review->file) {
                Storage::delete('document_review/' . $document_review->file);
            }

            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $request->file->storeAs('document_review', $fileName, 'public');

            $document_review->update([
                'file' => $fileName,
                'status' => 'Submitted',
            ]);
        }

        return response()->json('File uploaded successfully');
    }

    public function file(Request $request)
    {
        $document_review = DocumentReview::findOrFail($request->id);

        if ($document_review->file) {
            $fileUrl = asset('storage/document_review/' . $document_review->file);
            return response()->json(['file_url' => $fileUrl]);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}
