<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    protected $table = 'departemen';

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi dengan dokumen (melalui pengguna)
    public function dokumen()
    {
        return $this->hasManyThrough(IndukDokumen::class, User::class);
    }
    public function documents()
{
    return $this->belongsToMany(IndukDokumen::class, 'document_department');
}

}
