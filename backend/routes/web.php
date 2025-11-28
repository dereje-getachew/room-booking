<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Room Booking API is running',
        'status' => 'ok',
        'version' => '1.0.0',
        'endpoints' => [
            'health' => '/api/health',
            'auth' => '/api/auth/login',
            'rooms' => '/api/rooms'
        ]
    ]);
});
