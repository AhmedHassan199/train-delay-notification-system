<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('delay_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('speed_kmh', 6, 2)->nullable();
            $table->dateTime('computed_eta')->nullable();
            $table->integer('delay_minutes')->default(0);
            $table->boolean('notified')->default(false);
            $table->enum('source', ['sensor', 'admin'])->default('sensor');
            $table->json('payload')->nullable();
            $table->dateTime('reported_at');
            $table->timestamps();

            $table->index(['trip_id', 'reported_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delay_events');
    }
};
