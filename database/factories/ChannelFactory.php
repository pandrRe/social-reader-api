<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'type' => Arr::random(['rss', 'atom']),
            'xml_source' => fake()->url(),
            'md5_checksum' => fake()->md5(),
            'ttl' => 60,
        ];
    }

    public function rss() {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'rss',
            ];
        });
    }

    public function atom() {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'atom',
            ];
        });
    }
}
