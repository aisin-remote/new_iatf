<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DocumentControl;
use App\Models\Departemen;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Auth;

class DocumentControlController extends Controller
{
    public function list()
    {
        $departments = Departemen::orderBy('nama_departemen', 'ASC')->get();

        $document_controls = DocumentControl::orderBy('name', 'ASC')->get();
        
        return view('document_control.list', compact('departments', 'document_controls'));
    }

    public function list_ajax(Request $request)
    {
        $data = DocumentControl::orderBy('name', 'ASC');

        return DataTables::eloquent($data)->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'department' => 'required',
            'obsolete' => 'required',
            'set_reminder' => 'required',
        ]);

        try {
            $document_control = DocumentControl::create([
                'name' => $request->name,
                'department' => $request->department,
                'obsolete' => $request->obsolete,
                'set_reminder' => $request->set_reminder,
            ]);

            $document_control->save();

            return "Create Successfully";
        } catch (\Exception $e) {
            return $e->getMessage();
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
}
