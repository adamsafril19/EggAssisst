<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectionLog extends Model
{
    protected $fillable = [
        'date',
        'old_value',
        'new_value',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'old_value' => 'decimal:2',
        'new_value' => 'decimal:2',
    ];
}
