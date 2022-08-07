<?php

namespace App\Models;

use App\Services\RawChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Channel extends Model
{
    use HasFactory;

    public function rssChannel() {
        return $this->hasOne(RssChannel::class);
    }

    public function atomFeed() {
        return $this->hasOne(AtomFeed::class);
    }

    public function subscriptions() {
        return $this->hasMany(ChannelSubscription::class);
    }

    public function makeChannelDataModel($rawChannel): RssChannel | AtomFeed {
        if ($this->type === 'rss') {
            return RssChannel::makeFromRawChannel($rawChannel);
        }
        else {
            return AtomFeed::makeFromRawChannel($rawChannel);
        }
    }

    public function saveChannelDataModel(RssChannel | AtomFeed $channelDataModel) {
        if ($channelDataModel instanceof RssChannel) {
            return $this->rssChannel()->save($channelDataModel);
        }
        else {
            return $this->atomFeed()->save($channelDataModel);
        }
    }

    public static function fromRawChannel(RawChannel $rawChannel): Channel | null {
        $channel = new Channel();
        $channel->xml_source = $rawChannel->getSource();
        $channel->type = $rawChannel->isRss()? 'rss' : 'atom';
        $channel->md5_checksum = $rawChannel->getChecksum();

        $existingChannel = Channel::query()
            ->where('xml_source', $channel->xml_source)
            ->first();
        if ($existingChannel instanceof Channel) {
            return $existingChannel;
        }

        $channelDataModel = $channel->makeChannelDataModel($rawChannel);

        DB::transaction(function () use ($channel, $channelDataModel) {
            $channel->save();
            $channel->saveChannelDataModel($channelDataModel);
        });

        return $channel;
    }
}
