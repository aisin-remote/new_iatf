<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditControl extends Model
{
    use HasFactory;
    protected $table = 'audit_control';
    protected $fillable = [
        'dokumenaudit_id',
        'audit_id',
        'reminder',
        'duedate',
        'attachment',
    ];
    public function documentAudit()
    {
        return $this->belongsTo(DocumentAudit::class, 'dokumenaudit_id');
    }

    // Relasi ke Audit
    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }
}
