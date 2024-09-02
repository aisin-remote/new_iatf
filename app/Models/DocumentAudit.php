<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAudit extends Model
{
    use HasFactory;
    protected $table = 'document_audit';
    
    public function itemAudit()
    {
        return $this->belongsTo(ItemAudit::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
}
