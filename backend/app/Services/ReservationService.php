<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ReservationService
{
    public function getUserReservations(User $user): Collection
    {
        return $user->reservations()->with('room')->get();
    }

    public function getAllReservations(): Collection
    {
        return Reservation::with(['user', 'room'])->get();
    }

    public function createReservation(User $user, array $data): Reservation
    {
        $this->validateNoOverlap($data['room_id'], $data['start_time'], $data['end_time']);

        return $user->reservations()->create($data);
    }

    public function cancelReservation(User $user, int $reservationId): void
    {
        $reservation = $user->reservations()->findOrFail($reservationId);
        
        // Check if reservation starts more than 24 hours from now
        $startTime = new \DateTime($reservation->start_time);
        $now = new \DateTime();
        $interval = $now->diff($startTime);
        
        if ($interval->days < 1 || ($interval->days === 1 && $interval->h < 24)) {
            throw new \Exception('Reservations can only be cancelled if the start time is more than 24 hours away.');
        }
        
        $reservation->delete();
    }

    public function modifyReservation(User $user, int $reservationId, array $data): Reservation
    {
        $reservation = $user->reservations()->findOrFail($reservationId);
        
        // Check if reservation starts more than 24 hours from now
        $startTime = new \DateTime($reservation->start_time);
        $now = new \DateTime();
        $interval = $now->diff($startTime);
        
        if ($interval->days < 1 || ($interval->days === 1 && $interval->h < 24)) {
            throw new \Exception('Reservations can only be modified if the start time is more than 24 hours away.');
        }
        
        // Validate new room availability if changing room or time
        if (isset($data['room_id']) || isset($data['start_time']) || isset($data['end_time'])) {
            $newRoomId = $data['room_id'] ?? $reservation->room_id;
            $newStartTime = $data['start_time'] ?? $reservation->start_time;
            $newEndTime = $data['end_time'] ?? $reservation->end_time;
            
            // Exclude current reservation from overlap check
            $this->validateNoOverlapForModification($newRoomId, $newStartTime, $newEndTime, $reservationId);
        }
        
        $reservation->update($data);
        $reservation->load('room');
        
        return $reservation;
    }

    public function cancelReservationAsAdmin(int $reservationId): void
    {
        $reservation = Reservation::findOrFail($reservationId);
        $reservation->delete();
    }

    protected function validateNoOverlap(int $roomId, string $startTime, string $endTime): void
    {
        $exists = Reservation::where('room_id', $roomId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->exists();

        if ($exists) {
            throw new \Exception('This room is already booked for the selected time.');
        }
    }

    protected function validateNoOverlapForModification(int $roomId, string $startTime, string $endTime, int $excludeReservationId): void
    {
        $exists = Reservation::where('room_id', $roomId)
            ->where('id', '!=', $excludeReservationId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->exists();

        if ($exists) {
            throw new \Exception('This room is already booked for the selected time.');
        }
    }
}
