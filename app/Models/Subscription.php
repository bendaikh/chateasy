<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'amount',
        'status',
        'starts_at',
        'ends_at',
        'features',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'features' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
