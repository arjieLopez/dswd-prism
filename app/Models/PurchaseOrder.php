<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'supplier_id',
        'po_number',
        'mode_of_procurement',
        'delivery_term',
        'payment_term',
        'date_of_delivery',
        'status_id',
        'generated_at',
        'generated_by',
        'completed_at'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'completed_at' => 'datetime',
        'date_of_delivery' => 'date'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
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

    public function getStatusAttribute()
    {
        $statusRelation = $this->getRelationValue('status');
        return $statusRelation ? $statusRelation->name : null;
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
}
