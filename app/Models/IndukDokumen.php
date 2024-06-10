<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndukDokumen extends Model
{
    use HasFactory;
    protected $table = 'indukdokumen';
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

    public function getFilePathAttribute()
    {
        return asset('dokumen/' . $this->file);
    }

    public static function generateNomorDokumen($tipe, $departemen_user, $rule_id, $nomor_revisi)
    {
        // Mendapatkan informasi rule dari rule_id
        $rule = RuleCode::find($rule_id);
        if (!$rule) {
            throw new \Exception('RuleDoc tidak ditemukan.');
        }

        $kode_proses = $rule->kode_proses;

        $prefix = strtoupper(substr($tipe, 0, 2));

        // Mendapatkan dokumen terbaru dengan tipe_dokumen dan rule_id yang sama
        $latestDokumen = self::where('tipe_dokumen', $tipe)
            ->where('rule_id', $rule_id)
            ->orderBy('id', 'desc')
            ->first();

        // Menghasilkan nomor list dokumen
        $number = $latestDokumen ? intval(substr($latestDokumen->nomor_dokumen, -6, 4)) + 1 : 1;

        // Format nomor dokumen
        $nomorDokumen = sprintf(
            '%s-%s-%s-%04d-%02d',
            $prefix,
            strtoupper($departemen_user),
            strtoupper($kode_proses),
            $number,
            $nomor_revisi
        );

        return $nomorDokumen;
    }
}
