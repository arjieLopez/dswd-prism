<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'tin',
        'address',
        'contact_person',
        'contact_number',
        'email',
        'status'
    ];

    public function getStatusColorAttribute()
    {
        return $this->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
