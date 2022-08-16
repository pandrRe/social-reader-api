<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AtomFeed>
 */
class AtomFeedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'atom_id' => fake()->uuid(),
            'title' => fake()->sentence(),
            'updated' => now(),
            'self_link' => fake()->url(),
            'alternate_link' => fake()->url(),
            'subtitle' => fake()->sentence(),
            'icon' => fake()->imageUrl(64, 64, 'icon'),
            'logo' => fake()->imageUrl(300, 150, 'logo'),
        ];
    }
}
