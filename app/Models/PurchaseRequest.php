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
        'stoc_property_no',
        'unit',
        'item_description',
        'quantity',
        'unit_cost',
        'total_cost',
        'total',
        'delivery_period',
        'delivery_address',
        'purpose',
        'requested_by_name',
        'requested_by_designation',
        'requested_by_signature',
        'approved_by_name',
        'approved_by_designation',
        'approved_by_signature',
        'status',
        'remarks',
        'notes',
        'po_number',
        'po_generated_at',
        'po_generated_by',
        'supplier_id',
        'mode_of_procurement',
        'delivery_term',
        'payment_term',
        'date_of_delivery'
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'po_generated_at' => 'datetime',
        'date_of_delivery' => 'date'
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
            'failed' => 'bg-red-100 text-red-800',
            'po_generated' => 'bg-blue-100 text-blue-800',
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
            'failed' => 'Failed',
            default => ucfirst($this->status),
        };
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
