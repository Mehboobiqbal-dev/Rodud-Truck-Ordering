<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_location',
        'delivery_location',
        'cargo_size',
        'cargo_weight',
        'notes',
        'pickup_datetime',
        'delivery_datetime',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pickup_datetime' => 'datetime',
            'delivery_datetime' => 'datetime',
            'cargo_weight' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
}
