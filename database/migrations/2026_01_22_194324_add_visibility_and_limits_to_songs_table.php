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
        Schema::table('songs', function (Blueprint $table) {
            $table->timestamp('visible_from')->nullable()->after('is_pro_only');
            $table->timestamp('visible_until')->nullable()->after('visible_from');
            $table->integer('download_limit')->default(2)->after('visible_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['visible_from', 'visible_until', 'download_limit']);
        });
    }
};
