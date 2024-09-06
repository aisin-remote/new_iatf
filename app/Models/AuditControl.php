<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditControl extends Model
{
    use HasFactory;
    protected $table = 'audit_control';
    protected $fillable = [
        'departemen_id',
        'item_audit_id',
        'attachment'
    ];

    public function itemAudit()
    {
        return $this->belongsTo(ItemAudit::class, 'item_audit_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
    public function documentAudit()
    {
        return $this->hasMany(DocumentAuditControl::class, 'audit_control_id');
    }
}
