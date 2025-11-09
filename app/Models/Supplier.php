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
        'status_id'
    ];

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

    public function isActive()
    {
        $statusRelation = $this->getRelationValue('status');
        if ($statusRelation) {
            return $statusRelation->name === 'active';
        }
        // If relationship not loaded, find the status by ID
        if ($this->status_id) {
            $status = \App\Models\Status::find($this->status_id);
            return $status && $status->name === 'active';
        }
        return false;
    }

    // Backward-compatible accessor for normalized status column
    public function getStatusAttribute()
    {
        $statusRelation = $this->getRelationValue('status');
        return $statusRelation ? $statusRelation->name : null;
    }

    // Reference table relationships
    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }
}
