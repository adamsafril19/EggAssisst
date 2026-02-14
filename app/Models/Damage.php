<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    protected $fillable = [
        'date',
        'kg',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
        'kg' => 'decimal:2',
    ];
}
