<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'order' => $this->faker->numberBetween(1, 10),
            'content' => $this->faker->paragraphs(3, true),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ];
    }
}
