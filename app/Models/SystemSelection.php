<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
    ];

    /**
     * Get items by type
     */
    public static function getByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('type', $type)->orderBy('name')->get();
    }

    /**
     * Get all available types
     */
    public static function getTypes(): array
    {
        return [
            'metric_units' => 'Metric Units',
            'entity' => 'Entity',
            'fund_cluster' => 'Fund Cluster',
            'responsibility_code' => 'Responsibility Code',
            'delivery_period' => 'Delivery Period',
            'delivery_address' => 'Delivery Address',
            'mode_of_procurement' => 'Mode of Procurement',
            'delivery_term' => 'Delivery Term',
            'payment_term' => 'Payment Term',
        ];
    }
}
