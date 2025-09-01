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
        Schema::create('equipment_model_user', function (Blueprint $table) {
            $table->primary(['user_id', 'equipment_model_id']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_model_id')->constrained('equipment_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_model_user');
    }
};
