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

    public function getStatusAttribute()
    {
        return $this->purchaseRequest ? $this->purchaseRequest->status : null;
    }

    public function getStatusDisplayAttribute()
    {
        return $this->purchaseRequest ? $this->purchaseRequest->status_display : 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        return $this->purchaseRequest ? $this->purchaseRequest->status_color : 'bg-gray-100 text-gray-800';
    }
}
