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
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'failed' => 'red',
            default => 'gray',
        };
    }
}
