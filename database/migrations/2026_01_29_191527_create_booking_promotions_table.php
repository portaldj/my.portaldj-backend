<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('press_release'); // The note from the DJ
            $table->string('blog_url')->nullable(); // Set when approved
            $table->text('rejection_reason')->nullable(); // Set when rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_promotions');
    }
};
