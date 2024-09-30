<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentControl extends Model
{
    use HasFactory;

    protected $table = 'document_controls';

    protected $guarded = ['id'];
}
