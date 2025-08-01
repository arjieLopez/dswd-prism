<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pr_number',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
