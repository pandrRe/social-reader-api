<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ChannelSubscriptionTest extends TestCase
{
    public function test_that_subscription_to_a_new_channel_is_created()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/subscription', [
            'xml_source' => 'https://news.google.com/atom'
        ]);
        $response->assertOk();
    }
}
