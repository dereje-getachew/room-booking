<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'beds' => $this->beds,
            'location' => $this->location,
            'is_active' => $this->is_active,
            'image_url' => $this->image_url ? url('storage/' . $this->image_url) : null,
            'price_per_night' => $this->price_per_night,
            'room_type' => $this->room_type,
            'bed_type' => $this->bed_type,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'current_booking' => $this->when($this->relationLoaded('reservations') && $this->reservations->isNotEmpty(), function () {
                $currentBooking = $this->reservations->first();
                return [
                    'id' => $currentBooking->id,
                    'start_time' => $currentBooking->start_time,
                    'end_time' => $currentBooking->end_time,
                    'status' => $currentBooking->status,
                    'user' => [
                        'name' => $currentBooking->user->name ?? 'Guest'
                    ]
                ];
            }),
            'status' => $this->when(true, function () {
                // If room is inactive, it's unavailable
                if (!$this->is_active) {
                    return 'unavailable';
                }
                
                // Check if there are any active reservations (current or future)
                if ($this->relationLoaded('reservations') && $this->reservations->isNotEmpty()) {
                    $booking = $this->reservations->first();
                    $now = \Carbon\Carbon::now();
                    
                    // If currently within the booking time and checked in, room is occupied
                    if ($booking->start_time <= $now && $booking->end_time > $now && $booking->status === 'checked_in') {
                        return 'occupied';
                    }
                    
                    // If there's any active/confirmed reservation (current or future), room is booked
                    if (in_array($booking->status, ['confirmed', 'pending', 'checked_in'])) {
                        return 'booked';
                    }
                }
                
                // Otherwise, room is available
                return 'available';
            }),
        ];
    }
}
