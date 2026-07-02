<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->text('body');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index('to');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
