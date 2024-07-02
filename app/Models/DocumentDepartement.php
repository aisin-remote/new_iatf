<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDepartement extends Model
{
    use HasFactory;
    protected $table = 'document_departement';

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
    public function indukDokumen()
    {
        return $this->belongsTo(IndukDokumen::class, 'induk_dokumen_id');
    }
}
