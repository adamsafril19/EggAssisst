<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'date',
        'kg',
        'price_per_kg',
    ];

    protected $casts = [
        'date' => 'date',
        'kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
    ];
}
