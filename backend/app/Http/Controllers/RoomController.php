<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\Reservation;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function __construct(protected RoomService $roomService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $onlyActive = !$user || $user->role !== 'admin';

        $rooms = $this->roomService->getAllRooms($onlyActive, $request);

        return RoomResource::collection($rooms);
    }

    public function store(StoreRoomRequest $request)
    {
        $room = $this->roomService->createRoom($request->validated());

        return new RoomResource($room);
    }

    public function show(Room $room)
    {
        return new RoomResource($room);
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room = $this->roomService->updateRoom($room, $request->validated());

        return new RoomResource($room);
    }

    public function destroy(Room $room)
    {
        $this->roomService->deleteRoom($room);

        return response()->json(null, 204);
    }

    public function currentBooking(Room $room)
    {
        $currentBooking = Reservation::where('room_id', $room->id)
            ->where('start_time', '<=', Carbon::now())
            ->where('end_time', '>', Carbon::now())
            ->with('user')
            ->first();

        if (!$currentBooking) {
            return response()->json(null, 404);
        }

        return response()->json([
            'id' => $currentBooking->id,
            'start_time' => $currentBooking->start_time,
            'end_time' => $currentBooking->end_time,
            'user' => [
                'name' => $currentBooking->user->name
            ]
        ]);
    }
}
