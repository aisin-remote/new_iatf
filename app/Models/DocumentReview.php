<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReview extends Model
{
    use HasFactory;
    protected $table = 'document_reviews';
    protected $guarded = ['id'];
}
