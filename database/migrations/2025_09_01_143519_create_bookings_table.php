<?php

use App\Models\Booking;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dj_user_id')->constrained('users')->onDelete('cascade');
            $table->string('event_details');
            $table->dateTime('requested_date');
            $table->enum('status', [Booking::STATUS_PENDING, Booking::STATUS_ACCEPTED, Booking::STATUS_REJECTEDD])->default(Booking::STATUS_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
