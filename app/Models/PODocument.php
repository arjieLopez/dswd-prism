<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PODocument extends Model
{
    use HasFactory;

    protected $table = 'po_documents';

    protected $fillable = [
        'user_id',
        'po_number',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
