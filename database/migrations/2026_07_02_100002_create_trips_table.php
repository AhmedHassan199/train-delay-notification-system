<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained()->cascadeOnDelete();
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->decimal('route_distance_km', 8, 2)->nullable();

            $table->enum('status', ['on_time', 'delayed', 'cancelled', 'departed', 'arrived'])
                ->default('on_time')
                ->index();
            $table->unsignedInteger('delay_minutes')->default(0);

            $table->dateTime('expected_departure_time')->nullable();
            $table->dateTime('expected_arrival_time')->nullable();

            $table->decimal('last_distance_km', 8, 2)->nullable();
            $table->decimal('last_speed_kmh', 6, 2)->nullable();
            $table->dateTime('last_reading_at')->nullable();

            $table->unsignedInteger('last_notified_delay_minutes')->default(0);

            $table->unsignedInteger('available_seats')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
