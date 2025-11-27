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
            'image_url' => $this->image_url,
            'price_per_night' => $this->price_per_night,
            'room_type' => $this->room_type,
            'bed_type' => $this->bed_type,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
