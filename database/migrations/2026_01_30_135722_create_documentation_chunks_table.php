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
        Schema::create('documentation_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_model_id')->constrained()->cascadeOnDelete();
            $table->string('topic');
            $table->text('content');
            $table->text('embedding')->nullable(); // For future usage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_chunks');
    }
};
