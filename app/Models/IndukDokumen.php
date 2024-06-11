<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class IndukDokumen extends Model
{
    use HasFactory;
    protected $table = 'induk_dokumen';
    protected $fillable = [
        'nama_dokumen',
        'nomor_dokumen',
        'file',
        'user_id',
        'dokumen_id',
        'rule_id',
        'tgl_upload',
        'revisi_log',
        'status'
    ];

    // Relasi dengan pengguna
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan departemen (melalui user)
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'user_id', 'departemen_id');
    }
}
