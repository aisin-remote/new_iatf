<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndukDokumen extends Model
{
    use HasFactory;
    protected $table = 'induk_dokumen';

    protected $fillable = [
        'nomor_dokumen',
        'nama_dokumen',
        'user_id', 
        'dokumen_id', 
        'rule_id', 
        'tgl_upload', 
        'file', 
        'status', 
        'revisi_log', 
    ];
}
