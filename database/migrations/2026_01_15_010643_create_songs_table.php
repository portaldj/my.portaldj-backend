<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('artist_name');
            $table->string('track_name');
            $table->integer('bpm')->nullable();
            $table->string('key')->nullable();
            $table->string('remix_type')->nullable(); // Original, Remix, Edit
            $table->foreignId('genre_id')->nullable()->constrained('music_genres')->nullOnDelete();
            $table->string('file_path');
            $table->boolean('is_pro_only')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
