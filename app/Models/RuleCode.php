<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleCode extends Model
{
    use HasFactory;

    protected $table = 'rule';
    protected $fillable = [
        'kode_proses',
        'nama_proses'
    ];
}
