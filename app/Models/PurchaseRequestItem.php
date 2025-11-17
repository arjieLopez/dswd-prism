<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'unit_id',
        'quantity',
        'unit_cost',
        'item_description',
        'total_cost',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    // Backward-compatible accessor for normalized unit column
    public function getUnitAttribute()
    {
        $unitRelation = $this->getRelationValue('unit');
        return $unitRelation ? $unitRelation->name : null;
    }

    // Reference table relationships
    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class)->withTrashed();
    }
}
