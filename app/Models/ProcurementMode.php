<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementMode extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }
}
