<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_id'); // The home this device belongs to
            $table->string('name');
            $table->enum('type', ['camera', 'door_lock', 'window_sensor', 'lamp', 'fire_alarm']);
            $table->string('status', 50); // e.g., 'online', 'locked', 'on'
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('devices');
    }
};