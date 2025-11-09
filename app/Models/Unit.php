<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbreviation'];

    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }
}
