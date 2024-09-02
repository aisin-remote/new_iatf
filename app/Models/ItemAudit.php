<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAudit extends Model
{
    protected $table = 'item_audit';
    use HasFactory;
    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function dokumens()
    {
        return $this->hasMany(DocumentAudit::class);
    }

    public function departemens()
    {
        return $this->belongsToMany(Departemen::class, 'audit_departemen');
    }
}
