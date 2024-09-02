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
        return $this->belongsToMany(User::class, 'departemen_user', 'departemen_id', 'user_id');
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
    public function itemAudits()
    {
        return $this->belongsToMany(ItemAudit::class, 'audit_departemen');
    }
}
