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
        Schema::table('dj_equipments', function (Blueprint $table) {
            $table->foreignId('equipment_model_id')->nullable()->constrained()->nullOnDelete()->after('user_id');

            // Allow nulls for transition or custom items
            $table->foreignId('brand_id')->nullable()->change();
            $table->foreignId('equipment_type_id')->nullable()->change();
            $table->string('model_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dj_equipments', function (Blueprint $table) {
            $table->dropForeign(['equipment_model_id']);
            $table->dropColumn('equipment_model_id');

            // Revert (this might fail if data exists with nulls, but for strict rollback we attempt it)
            // Note: SQLite might struggle with modifying columns, but we need to try. 
            // If fails, we accept strict rollback limitation.
        });
    }
};
