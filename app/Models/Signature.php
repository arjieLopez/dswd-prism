<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'signable_type',
        'signable_id',
        'signature_path',
        'signed_at'
    ];

    protected $casts = [
        'signed_at' => 'datetime'
    ];

    public function signable()
    {
        return $this->morphTo();
    }
}
