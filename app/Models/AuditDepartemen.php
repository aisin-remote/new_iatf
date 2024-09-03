<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditDepartemen extends Model
{
    use HasFactory;
    protected $table = 'audit_departemen';
    protected $fillable = [
        'departemen_id',
        'item_audit_id',
        'attachment'
    ];

    public function itemAudits()
    {
        return $this->hasMany(ItemAudit::class, 'audit_departemen_id');
    }
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
}
