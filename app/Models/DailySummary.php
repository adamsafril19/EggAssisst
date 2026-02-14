<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySummary extends Model
{
    protected $fillable = [
        'date',
        'total_sold',
        'profit_est',
        'closing_stock',
    ];

    protected $casts = [
        'date' => 'date',
        'total_sold' => 'decimal:2',
        'profit_est' => 'decimal:2',
        'closing_stock' => 'decimal:2',
    ];
}
