<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomService
{
    public function getAllRooms(bool $onlyActive = false, Request $request = null)
    {
        $query = Room::query();

        if ($onlyActive) {
            $query->active();
        }

        // Search functionality
        if ($request && $search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('beds', 'like', "%{$search}%")
                  ->orWhere('room_type', 'like', "%{$search}%")
                  ->orWhere('bed_type', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Pagination
        if ($request) {
            $perPage = $request->get('per_page', 10);
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function createRoom(array $data): Room
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('rooms', 'public');
        }

        return Room::create($data);
    }

    public function updateRoom(Room $room, array $data): Room
    {
        if (isset($data['image'])) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $data['image'] = $data['image']->store('rooms', 'public');
        }

        $room->update($data);

        return $room;
    }

    public function deleteRoom(Room $room): void
    {
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();
    }
}
