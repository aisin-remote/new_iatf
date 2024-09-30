<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    protected $table = 'departemen';
    protected $fillable = [
        'nama_departemen',
        'code',  // Tambahkan 'code' di sini
        // Properti lainnya
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id');
    }

    // Relasi dengan dokumen (melalui pengguna)
    public function dokumen()
    {
        return $this->hasManyThrough(IndukDokumen::class, User::class);
    }
    // public function documents()
    // {
    //     return $this->belongsToMany(IndukDokumen::class, 'document_department');
    // }
    public function indukDokumen()
    {
        return $this->belongsToMany(IndukDokumen::class, 'document_departement', 'departemen_id', 'induk_dokumen_id');
    }
    public function auditDepartemens()
    {
        return $this->hasMany(AuditControl::class, 'departemen_id');
    }
}
