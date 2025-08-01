<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'pr_number',
        'document_name',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionIconAttribute()
    {
        return match ($this->action) {
            'created_pr' => 'mdi:file-plus-outline',
            'updated_pr' => 'mdi:file-edit-outline',
            'uploaded_document' => 'mdi:upload-outline',
            default => 'mdi:information-outline'
        };
    }

    public function getActionColorAttribute()
    {
        return match ($this->action) {
            'created_pr' => 'text-green-600 bg-green-100',
            'updated_pr' => 'text-blue-600 bg-blue-100',
            'uploaded_document' => 'text-purple-600 bg-purple-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}
