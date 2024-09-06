<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAuditControl extends Model
{
    use HasFactory;
    protected $table = 'document_audit_control';
    protected $fillable = ['audit_control_id', 'attachment'];
    public function auditControl()
    {
        return $this->belongsTo(AuditControl::class, 'audit_control_id');
    }
}
