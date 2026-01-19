<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_and_update_profile()
    {
        $this->seed(\Database\Seeders\TaxonomySeeder::class);
        $this->seed(\Database\Seeders\AdminSeeder::class);

        $user = User::factory()->create();

        // Initial check - empty profile (or null)
        $response = $this->actingAs($user)->getJson('/api/profile');
        $response->assertStatus(200);

        // Update Profile
        $updatedData = [
            'username' => 'dj_test',
            'first_name' => 'Test',
            'last_name' => 'User',
            'social_networks' => [
                ['platform' => 'Instagram', 'url' => 'http://inst.com/test', 'order' => 1]
            ]
        ];

        $updateResponse = $this->actingAs($user)->postJson('/api/profile', $updatedData);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('profiles', ['username' => 'dj_test']);
        $this->assertDatabaseHas('social_networks', ['user_id' => $user->id, 'platform' => 'Instagram']);
    }

    public function test_feed_flow()
    {
        $this->seed(\Database\Seeders\TaxonomySeeder::class);
        $user = User::factory()->create();

        // Create Post
        $postResponse = $this->actingAs($user)->postJson('/api/feed', [
            'content' => 'Hello World'
        ]);
        $postResponse->assertStatus(201);
        $postId = $postResponse->json('id');

        // Comment
        $commentResponse = $this->actingAs($user)->postJson("/api/feed/{$postId}/comment", [
            'content' => 'Nice post'
        ]);
        $commentResponse->assertStatus(201);

        // Get Feed
        $feedResponse = $this->actingAs($user)->getJson('/api/feed');
        $feedResponse->assertStatus(200);
        $feedResponse->assertJsonFragment(['content' => 'Hello World']);
    }

    public function test_academy_flow()
    {
        $this->seed(\Database\Seeders\TaxonomySeeder::class);
        $course = Course::create(['title' => 'DJ 101', 'description' => 'Basics']);
        $chapter = $course->chapters()->create(['title' => 'Intro']);
        $exam = $chapter->exam()->create(['title' => 'Intro Exam']);
        $question = $exam->questions()->create(['question_text' => 'What is a beat?', 'points' => 10]);
        $answer = $question->answers()->create(['answer_text' => 'Rhythm', 'is_correct' => true]);

        $user = User::factory()->create();

        // Submit Exam
        $response = $this->actingAs($user)->postJson("/api/academy/exams/{$exam->id}/submit", [
            'answers' => [$question->id => $answer->id] // Sending answer ID
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('result.passed', true);
    }
}
