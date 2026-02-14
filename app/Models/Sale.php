<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'datetime',
        'kg',
        'price_per_kg',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
    ];
}
