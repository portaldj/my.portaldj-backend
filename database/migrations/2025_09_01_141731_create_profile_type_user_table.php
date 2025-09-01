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
        Schema::create('profile_type_user', function (Blueprint $table) {
            $table->primary(['user_id', 'profile_type_id']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_type_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_type_user');
    }
};
