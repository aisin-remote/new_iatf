<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'audit';
    public function auditControl()
    {
        return $this->hasMany(ItemAudit::class, 'audit_id');
    }
}
