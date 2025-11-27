<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('password', [AuthController::class, 'changePassword']);
    });
});

Route::middleware('auth:api')->group(function () {
    // Rooms
    Route::get('rooms', [RoomController::class, 'index']); // Public/Active for users, All for admin
    Route::post('rooms', [RoomController::class, 'store'])->middleware('admin'); // Admin only
    Route::put('rooms/{room}', [RoomController::class, 'update'])->middleware('admin'); // Admin only
    Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->middleware('admin'); // Admin only
    Route::get('rooms/{room}', [RoomController::class, 'show']);
    Route::get('rooms/{room}/current-booking', [RoomController::class, 'currentBooking']);

    // Reservations
    Route::get('reservations', [ReservationController::class, 'index']); // My reservations
    Route::post('reservations', [ReservationController::class, 'store']); // Create reservation
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy']); // Cancel reservation
    Route::put('reservations/{reservation}', [ReservationController::class, 'update']); // Modify reservation
    
    Route::get('admin/reservations', [ReservationController::class, 'indexAdmin'])->middleware('admin'); // All reservations (Admin only)
    Route::delete('admin/reservations/{reservation}', [ReservationController::class, 'destroyAdmin'])->middleware('admin'); // Admin cancel reservation
    
    // User Settings
    Route::get('user/settings', [AuthController::class, 'getSettings']);
    Route::put('user/settings', [AuthController::class, 'updateSettings']);
    Route::put('user/password', [AuthController::class, 'changePassword']);
});
