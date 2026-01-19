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
        Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->morphs('taggable'); // taggable_id, taggable_type
            $table->timestamps();
        });

        // Migrate existing tags
        // We'll do this using a raw query or model logic? Raw is safer in migration usually.
        // Assuming Post model might change code, raw DB is best.
        $posts = \Illuminate\Support\Facades\DB::table('posts')
            ->whereNotNull('location_tag_id')
            ->whereNotNull('location_tag_type')
            ->get();

        foreach ($posts as $post) {
            $type = $post->location_tag_type;
            // Map simple type to full class name
            $modelClass = match ($type) {
                'club' => 'App\\Models\\Club',
                'city' => 'App\\Models\\City',
                'user' => 'App\\Models\\User',
                default => null
            };

            if ($modelClass) {
                \Illuminate\Support\Facades\DB::table('post_tags')->insert([
                    'post_id' => $post->id,
                    'taggable_id' => $post->location_tag_id,
                    'taggable_type' => $modelClass,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tags');
    }
};
