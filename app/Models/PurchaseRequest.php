<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'office_id',
        'pr_number',
        'entity_name',
        'fund_cluster',
        'responsibility_center_code',
        'date',
        'submitted_at',
        'stoc_property_no',
        'total',
        'delivery_period',
        'delivery_address',
        'purpose',
        'status_id',
        'procurement_mode_id',
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

    public function office()
    {
        return $this->belongsTo(Office::class)->withTrashed();
    }

    public function getStatusColorAttribute()
    {
        $statusRelation = $this->getRelationValue('status');
        if ($statusRelation) {
            return $statusRelation->color;
        }
        // If relationship not loaded, find the status by ID
        if ($this->status_id) {
            $status = \App\Models\Status::find($this->status_id);
            return $status ? $status->color : 'bg-gray-100 text-gray-800';
        }
        return 'bg-gray-100 text-gray-800';
    }

    public function getStatusDisplayAttribute()
    {
        $statusRelation = $this->getRelationValue('status');
        if ($statusRelation) {
            return $statusRelation->display_name;
        }
        // If relationship not loaded, find the status by ID
        if ($this->status_id) {
            $status = \App\Models\Status::find($this->status_id);
            return $status ? $status->display_name : 'Unknown';
        }
        return 'Unknown';
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

    // Reference table relationships
    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }

    public function procurementMode()
    {
        return $this->belongsTo(\App\Models\ProcurementMode::class)->withTrashed();
    }
}
