<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Http\Request;

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
}
