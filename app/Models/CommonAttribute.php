<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'attribute_key',
        'attribute_value'
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
