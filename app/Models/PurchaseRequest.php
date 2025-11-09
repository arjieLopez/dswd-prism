<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pr_number',
        'entity_name',
        'fund_cluster',
        'office_section',
        'responsibility_center_code',
        'date',
        'submitted_at',
        'stoc_property_no',
        'total',
        'delivery_period',
        'delivery_address',
        'purpose',
        'status',
        'remarks',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
        'total' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'po_generated' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'po_generated' => 'PO Generated',
            'completed' => 'Completed',
            default => ucfirst($this->status),
        };
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signable');
    }

    public function commonAttributes()
    {
        return $this->morphMany(CommonAttribute::class, 'entity');
    }
}
