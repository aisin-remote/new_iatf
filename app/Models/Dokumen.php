<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'nomor_template', // Tambahkan 'nomor_template' ke sini
        'jenis_dokumen',
        'tipe_dokumen',
        'file',
        // tambahkan kolom lainnya sesuai kebutuhan
    ];

    public function indukDokumen()
    {
        return $this->hasMany(IndukDokumen::class, 'dokumen_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id');
    }
}
