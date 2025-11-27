use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Http\Resources\RoomResource;

// Create a user
$user = User::firstOrCreate(
    ['email' => 'test@example.com'],
    ['name' => 'Test User', 'password' => bcrypt('password')]
);

// Room 1: Available
$room1 = Room::create([
    'room_number' => '101-' . uniqid(),
    'beds' => 1,
    'price_per_night' => 100,
    'is_active' => true,
    'location' => 'Test Location',
]);

// Room 2: Booked
$room2 = Room::create([
    'room_number' => '102-' . uniqid(),
    'beds' => 1,
    'price_per_night' => 100,
    'is_active' => true,
    'location' => 'Test Location',
]);

Reservation::create([
    'user_id' => $user->id,
    'room_id' => $room2->id,
    'start_time' => now()->subHour(),
    'end_time' => now()->addDay(),
    'status' => 'confirmed',
]);

// Room 3: Occupied
$room3 = Room::create([
    'room_number' => '103-' . uniqid(),
    'beds' => 1,
    'price_per_night' => 100,
    'is_active' => true,
    'location' => 'Test Location',
]);

Reservation::create([
    'user_id' => $user->id,
    'room_id' => $room3->id,
    'start_time' => now()->subHour(),
    'end_time' => now()->addDay(),
    'status' => 'checked_in',
]);

// Fetch and display
$service = app(\App\Services\RoomService::class);
$rooms = $service->getAllRooms(false);
$resource = RoomResource::collection($rooms);

echo json_encode($resource->resolve());
