<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('location')->after('beds');
            $table->integer('capacity')->after('beds');
            $table->string('image')->nullable()->after('is_active');
        });
        
        // Update existing records
        \DB::statement('UPDATE rooms SET name = room_number, capacity = beds, location = "Building 1, Floor 1" WHERE name IS NULL');
        
        // Now drop the old columns
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_number');
            $table->dropColumn('beds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_number')->unique()->after('id');
            $table->integer('beds')->after('room_number');
        });
        
        // Restore old data
        \DB::statement('UPDATE rooms SET room_number = name, beds = capacity WHERE room_number IS NULL');
        
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['name', 'location', 'capacity', 'image']);
        });
    }
};
