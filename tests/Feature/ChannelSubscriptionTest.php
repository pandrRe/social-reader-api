<?php

namespace Tests\Feature;

use App\Models\AtomFeed;
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

    private $rssFeed = <<<EOT
    <?xml version="1.0" encoding="utf-8" standalone="yes"?>
    <rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
        <channel>
            <title>Test RSS Feed</title>
            <link>http://rssfeed.example/feed</link>
            <lastBuildDate>Sun, 07 Aug 2022 22:00:41 GMT</lastBuildDate>
            <description>This is a test RSS feed.</description>
            <item>
                <title>Test RSS Item</title>
                <link>http://rssfeed.example/item/1</link>
                <guid isPermaLink="false">111111111</guid>
                <pubDate>Sun, 07 Aug 2022 22:00:41 GMT</pubDate>
                <description>Item description.</description>
                <source url="https://rssfeed.example">RSS Example</source>
            </item>
        </channel>
    </rss>
    EOT;

    private function mockedAtomRawChannel() {
        $sp = new SimplePie();
        $sp->set_raw_data($this->atomFeed);
        return new RawChannel($sp);
    }

    private function mockedRssRawChannel() {
        $sp = new SimplePie();
        $sp->set_raw_data($this->rssFeed);
        return new RawChannel($sp);
    }

    private function mockNextChannelRead(RawChannel $channel) {
        $mockedRawChannelDescriptor = Mockery::mock(
            RawChannelDescriptor::class,
            function (MockInterface $mock) use ($channel) {
                $mock->shouldReceive('read')
                    ->andReturn($channel);
            }
        );

        $this->partialMock(RawChannelManager::class, function (MockInterface $mock) use ($mockedRawChannelDescriptor) {
            $mock->shouldReceive('createRawChannel')
                ->andReturn($mockedRawChannelDescriptor);
        });
    }

    public function tearDown(): void {
        Mockery::close();
    }

    public function test_that_subscription_to_a_new_atom_feed_is_created()
    {
        $this->mockNextChannelRead($this->mockedAtomRawChannel());

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

    public function test_that_subscription_to_a_new_rss_channel_is_created() {
        $this->mockNextChannelRead($this->mockedRssRawChannel());

        $user = User::factory()->create();

        $this->assertDatabaseMissing('channels', [
            'xml_source' => 'http://rssfeed.example/feed'
        ]);
        $response = $this->actingAs($user)->postJson('/api/subscription', [
            'xml_source' => 'http://rssfeed.example/feed'
        ]);
        $response->assertCreated();

        $channel = Channel::query()
            ->where('xml_source', 'http://rssfeed.example/feed')
            ->where('type', 'rss')
            ->first();
        $this->assertModelExists($channel);
        $this->assertDatabaseHas('rss_channels', [
            'title' => 'Test RSS Feed',
            'link' => 'http://rssfeed.example/feed',
            'channel_id' => $channel->id,
        ]);
        $this->assertDatabaseHas('channel_subscriptions', [
            'user_id' => $user->id,
            'channel_id' => $channel->id,
        ]);
    }

    public function test_that_subscription_to_existing_channel_is_created() {
        $channel = Channel::factory()->atom()->hasAtomFeed()->create();
        $user = User::factory()->create();

        $this->assertDatabaseMissing('channel_subscriptions', [
            'user_id' => $user->id,
            'channel_id' => $channel->id,
        ]);
        $response = $this->actingAs($user)->postJson('/api/subscription', [
            'xml_source' => $channel->xml_source,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('channel_subscriptions', [
            'user_id' => $user->id,
            'channel_id' => $channel->id,
        ]);
    }
}
