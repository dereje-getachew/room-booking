<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(protected ReservationService $reservationService)
    {
    }

    public function index(Request $request)
    {
        $reservations = $this->reservationService->getUserReservations($request->user());

        return ReservationResource::collection($reservations);
    }

    public function indexAdmin()
    {
        $reservations = $this->reservationService->getAllReservations();

        return ReservationResource::collection($reservations);
    }

    public function store(StoreReservationRequest $request)
    {
        try {
            $reservation = $this->reservationService->createReservation(
                $request->user(),
                $request->validated()
            );

            return new ReservationResource($reservation);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, $reservationId)
    {
        try {
            $this->reservationService->cancelReservation($request->user(), $reservationId);
            
            return response()->json([
                'message' => 'Reservation cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request, $reservationId)
    {
        try {
            $reservation = $this->reservationService->modifyReservation(
                $request->user(),
                $reservationId,
                $request->all()
            );

            return new ReservationResource($reservation);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroyAdmin(Request $request, $reservationId)
    {
        try {
            $this->reservationService->cancelReservationAsAdmin($reservationId);
            
            return response()->json([
                'message' => 'Reservation cancelled successfully by admin'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
