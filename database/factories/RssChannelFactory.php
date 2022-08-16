<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RssChannel>
 */
class RssChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'title' => fake()->sentence(),
            'link' => fake()->url(),
            'description' => fake()->paragraph(),
            'pub_date' => now(),
            'last_build_date' => now(),
            'image' => fake()->imageUrl(88, 31, 'rss'),
        ];
    }
}
