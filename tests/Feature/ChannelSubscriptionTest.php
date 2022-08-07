<?php

namespace Tests\Feature;

use App\Services\RawChannel;
use App\Services\RawChannelDescriptor;
use App\Services\RawChannelManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SimplePie\SimplePie;
use Tests\TestCase;
use App\Models\User;
use App\Models\Channel;
use Mockery\MockInterface;
use Mockery;

class ChannelSubscriptionTest extends TestCase {
    use RefreshDatabase;

    private $atomFeed = <<<EOT
    <?xml version="1.0" encoding="utf-8"?>
    <feed xmlns="http://www.w3.org/2005/Atom">
        <id>http://atomfeed.example/feed</id>
        <title>Test Atom Feed</title>
        <subtitle>This is a test atom feed.</subtitle>
        <updated>2022-08-07T19:04:39.000000000Z</updated>
        <link href="http://atomfeed.example/feed" rel="self" type="application/atom+xml"/>
    </feed>
    EOT;

    private function mockedAtomRawChannel() {
        $sp = new SimplePie();
        $sp->set_raw_data($this->atomFeed);
        return new RawChannel($sp);
    }

    public function tearDown(): void {
        Mockery::close();
    }

    public function test_that_subscription_to_a_new_atom_feed_is_created()
    {
        $mockedRawChannelDescriptor = Mockery::mock(RawChannelDescriptor::class, function (MockInterface $mock) {
            $mock->shouldReceive('read')
                ->once()
                ->andReturn($this->mockedAtomRawChannel());
        });

        $this->partialMock(RawChannelManager::class, function (MockInterface $mock) use ($mockedRawChannelDescriptor) {
            $mock->shouldReceive('createRawChannel')
                ->once()
                ->andReturn($mockedRawChannelDescriptor);
        });

        $user = User::factory()->create();

        $this->assertDatabaseMissing('channels', [
            'xml_source' => 'http://atomfeed.example/feed'
        ]);
        $response = $this->actingAs($user)->postJson('/api/subscription', [
            'xml_source' => 'http://atomfeed.example/feed'
        ]);
        $response->assertCreated();

        $channel = Channel::query()
            ->where('xml_source', 'http://atomfeed.example/feed')
            ->where('type', 'atom')
            ->first();
        $this->assertModelExists($channel);
        $this->assertDatabaseHas('atom_feeds', [
            'title' => 'Test Atom Feed',
            'self_link' => 'http://atomfeed.example/feed',
            'channel_id' => $channel->id,
        ]);
        $this->assertDatabaseHas('channel_subscriptions', [
            'user_id' => $user->id,
            'channel_id' => $channel->id,
        ]);
    }
}
