<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecommendingApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'first_name',
        'middle_name',
        'last_name',
        'designation_id'
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class, 'recommending_approval_office');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name .
            ($this->middle_name ? ' ' . $this->middle_name : '') .
            ' ' . $this->last_name;
    }
}
