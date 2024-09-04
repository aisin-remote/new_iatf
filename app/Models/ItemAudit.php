<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAudit extends Model
{
    protected $table = 'item_audit';
    protected $fillable = [
        'nama_item',
        'audit_id',
    ];
    use HasFactory;
    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }
    public function auditDepartemens()
    {
        return $this->hasMany(AuditControl::class, 'item_audit_id');
    }
    public function documents()
    {
        return $this->hasMany(DocumentAuditControl::class);
    }
}
