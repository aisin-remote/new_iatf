<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditControl;
use App\Models\Departemen;
use App\Models\DocumentAudit;
use App\Models\ItemAudit;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AuditController extends Controller
{
    


    
    public function update_auditControl(Request $request, $id)
    {
        $auditControl = AuditControl::findOrFail($id);
        $auditControl->dokumenaudit_id = $request->input('documentaudit_id');
        $auditControl->audit_id = $request->input('audit_id');
        $auditControl->attachment = $request->input('attachment');
        $auditControl->save();
        Alert::success('Success', 'Audit control changed succesfully.');
        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('auditControl');
    }
}
