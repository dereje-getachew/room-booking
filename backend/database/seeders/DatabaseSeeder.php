<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@roombooker.com'],
            [
                'name' => 'Eleanor Vance',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create regular user if doesn't exist
        User::firstOrCreate(
            ['email' => 'user@roombooker.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $rooms = [
            ['room_number' => 'Room 101', 'beds' => 1, 'location' => 'Building 1, Floor 1', 'is_active' => true, 'price_per_night' => 99, 'room_type' => 'Standard', 'bed_type' => 'Single', 'description' => 'Cozy single room perfect for solo travelers'],
            ['room_number' => 'Room 102', 'beds' => 1, 'location' => 'Building 1, Floor 1', 'is_active' => true, 'price_per_night' => 89, 'room_type' => 'Standard', 'bed_type' => 'Single', 'description' => 'Comfortable single room with city view'],
            ['room_number' => 'Room 201', 'beds' => 2, 'location' => 'Building 1, Floor 2', 'is_active' => true, 'price_per_night' => 149, 'room_type' => 'Standard', 'bed_type' => 'Double', 'description' => 'Spacious room with double bed'],
            ['room_number' => 'Room 202', 'beds' => 2, 'location' => 'Building 1, Floor 2', 'is_active' => true, 'price_per_night' => 159, 'room_type' => 'Deluxe', 'bed_type' => 'Queen', 'description' => 'Deluxe room with queen bed'],
            ['room_number' => 'King Suite 301', 'beds' => 1, 'location' => 'Building 3, Floor 1', 'is_active' => true, 'price_per_night' => 299, 'room_type' => 'Suite', 'bed_type' => 'King', 'description' => 'Luxury suite with king bed and separate living area'],
            ['room_number' => 'Executive Suite 302', 'beds' => 2, 'location' => 'Building 3, Floor 1', 'is_active' => true, 'price_per_night' => 399, 'room_type' => 'Executive', 'bed_type' => 'King', 'description' => 'Executive suite with king bed and workspace'],
            ['room_number' => 'Presidential Suite 401', 'beds' => 2, 'location' => 'Building 4, Floor 1', 'is_active' => true, 'price_per_night' => 599, 'room_type' => 'Suite', 'bed_type' => 'King', 'description' => 'Presidential suite with premium amenities'],
            ['room_number' => 'Twin Room 203', 'beds' => 2, 'location' => 'Building 1, Floor 2', 'is_active' => true, 'price_per_night' => 139, 'room_type' => 'Standard', 'bed_type' => 'Twin', 'description' => 'Room with two twin beds'],
            ['room_number' => 'Deluxe King 303', 'beds' => 1, 'location' => 'Building 3, Floor 1', 'is_active' => true, 'price_per_night' => 249, 'room_type' => 'Deluxe', 'bed_type' => 'King', 'description' => 'Deluxe room with king bed'],
            ['room_number' => 'Family Suite 402', 'beds' => 3, 'location' => 'Building 4, Floor 1', 'is_active' => false, 'price_per_night' => 449, 'room_type' => 'Suite', 'bed_type' => 'King', 'description' => 'Family suite with multiple beds'],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['room_number' => $room['room_number']],
                $room
            );
        }
    }
}