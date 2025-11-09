<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'approver_id',
        'status',
        'approved_at',
        'remarks',
        'signature_path'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signable');
    }
}
