<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomService
{
    public function getAllRooms(bool $onlyActive = false, Request $request = null)
    {
        $query = Room::query()->orderBy('created_at', 'desc');
        
        // Eager load active reservations (current and upcoming) with user to avoid N+1
        $query->with(['reservations' => function ($q) {
            $q->where('end_time', '>', \Carbon\Carbon::now())
              ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
              ->orderBy('start_time', 'asc')
              ->with('user');
        }]);

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

        // Advanced Filtering
        if ($request) {
            if ($beds = $request->get('beds')) {
                $query->where('beds', $beds);
            }
            if ($bedType = $request->get('bed_type')) {
                $query->where('bed_type', 'like', "%{$bedType}%");
            }
            if ($roomType = $request->get('room_type')) {
                $query->where('room_type', 'like', "%{$roomType}%");
            }
            
            // Status filtering
            if ($status = $request->get('status')) {
                switch ($status) {
                    case 'available':
                        // Rooms with no active reservations
                        $query->whereDoesntHave('reservations', function ($q) {
                            $q->where('end_time', '>', \Carbon\Carbon::now())
                              ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
                        });
                        break;
                    case 'booked':
                        // Rooms with confirmed/pending reservations
                        $query->whereHas('reservations', function ($q) {
                            $q->where('end_time', '>', \Carbon\Carbon::now())
                              ->whereIn('status', ['pending', 'confirmed']);
                        });
                        break;
                    case 'occupied':
                        // Rooms with checked-in reservations currently happening
                        $query->whereHas('reservations', function ($q) {
                            $now = \Carbon\Carbon::now();
                            $q->where('start_time', '<=', $now)
                              ->where('end_time', '>', $now)
                              ->where('status', 'checked_in');
                        });
                        break;
                    case 'unavailable':
                        // Inactive rooms
                        $query->where('is_active', false);
                        break;
                }
            }
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
        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image_url'] = $data['image']->store('rooms', 'public');
            unset($data['image']); // Remove the file object from data
        }

        return Room::create($data);
    }

    public function updateRoom(Room $room, array $data): Room
    {
        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($room->image_url) {
                Storage::disk('public')->delete($room->image_url);
            }
            $data['image_url'] = $data['image']->store('rooms', 'public');
            unset($data['image']); // Remove the file object from data
        }

        $room->update($data);

        return $room;
    }

    public function deleteRoom(Room $room): void
    {
        if ($room->image_url) {
            Storage::disk('public')->delete($room->image_url);
        }

        $room->delete();
    }
}
