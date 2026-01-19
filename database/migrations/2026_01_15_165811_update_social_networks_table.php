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
        Schema::table('social_networks', function (Blueprint $table) {
            $table->foreignId('social_platform_id')->after('user_id')->constrained()->cascadeOnDelete();
            $table->dropColumn('platform'); // Remove the old string column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_networks', function (Blueprint $table) {
            $table->string('platform')->after('user_id');
            $table->dropForeign(['social_platform_id']);
            $table->dropColumn('social_platform_id');
        });
    }
};
