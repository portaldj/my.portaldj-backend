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
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('commentable_id')->nullable()->after('id');
            $table->string('commentable_type')->nullable()->after('commentable_id');
            $table->index(['commentable_id', 'commentable_type']);
        });

        // Migrate existing data
        DB::table('comments')->update([
            'commentable_type' => 'App\\Models\\Post',
            'commentable_id' => DB::raw('post_id')
        ]);

        Schema::table('comments', function (Blueprint $table) {
            // Now enforce not null
            $table->unsignedBigInteger('commentable_id')->nullable(false)->change();
            $table->string('commentable_type')->nullable(false)->change();

            $table->dropForeign(['post_id']);
            $table->dropColumn('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('post_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Restore existing data (assuming only Posts were used)
        DB::table('comments')->where('commentable_type', 'App\\Models\\Post')->update([
            'post_id' => DB::raw('commentable_id')
        ]);

        // Clean up any non-Post comments (destructive in reverse)
        DB::table('comments')->whereNull('post_id')->delete();

        // Enforce not null
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->nullable(false)->change();
            $table->dropIndex(['commentable_id', 'commentable_type']);
            $table->dropColumn(['commentable_id', 'commentable_type']);
        });
    }
};
