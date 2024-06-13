<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    public function indukDokumen()
    {
        return $this->hasMany(IndukDokumen::class, 'dokumen_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

