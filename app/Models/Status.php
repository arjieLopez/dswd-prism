<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['context', 'name', 'display_name', 'color'];

    // Relationships
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
