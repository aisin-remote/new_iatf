<?php

namespace App\Models;

use App\Notifications\DocumentStatusChanged;
use App\Notifications\UserNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

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
        'status',
        'statusdoc'
    ];

    // Relasi dengan pengguna (user)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi dengan dokumen
    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id');
    }

    // Relasi dengan kode proses (rule)
    public function rule()
    {
        return $this->belongsTo(RuleCode::class, 'rule_id');
    }

    // Relasi many-to-many dengan departemen melalui tabel pivot document_departement
    public function departments()
    {
        return $this->belongsToMany(Departemen::class, 'document_departement', 'induk_dokumen_id', 'departemen_id');
    }

    // Metode untuk mendapatkan nama departemen yang tersebar
    public function getDepartemenTersebar()
    {
        return $this->departments()->pluck('nama_departemen')->implode(', ');
    }
}
