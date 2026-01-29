<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChapterCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_comment_on_chapter()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)->post(route('academy.chapters.comments.store', $chapter), [
            'content' => 'Great chapter!'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'commentable_id' => $chapter->id,
            'commentable_type' => Chapter::class,
            'content' => 'Great chapter!',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_comment_on_chapter()
    {
        $course = Course::factory()->create();
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $response = $this->post(route('academy.chapters.comments.store', $chapter), [
            'content' => 'Great chapter!'
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_comment_validation()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)->post(route('academy.chapters.comments.store', $chapter), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors('content');
    }
}
