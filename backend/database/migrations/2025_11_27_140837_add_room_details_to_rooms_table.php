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
            $table->decimal('price_per_night', 8, 2)->after('capacity')->default(100.00);
            $table->string('room_type')->after('price_per_night')->default('Standard');
            $table->string('bed_type')->after('room_type')->default('Double');
            $table->text('description')->after('bed_type')->nullable();
            // Rename columns to match expected structure
            $table->renameColumn('name', 'room_number');
            $table->renameColumn('capacity', 'beds');
            $table->renameColumn('image', 'image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['price_per_night', 'room_type', 'bed_type', 'description']);
            // Rename columns back
            $table->renameColumn('room_number', 'name');
            $table->renameColumn('beds', 'capacity');
            $table->renameColumn('image_url', 'image');
        });
    }
};
