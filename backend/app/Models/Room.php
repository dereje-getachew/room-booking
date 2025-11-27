<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'beds',
        'is_active',
        'image_url',
        'price_per_night',
        'room_type',
        'bed_type',
        'description',
        'location',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
