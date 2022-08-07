<?php

namespace App\Models;

use App\Services\RawChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RssChannel extends Model
{
    use HasFactory;

    public function language() {
        return $this->belongsTo(Language::class);
    }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public static function makeFromRawChannel(RawChannel $rawChannel) {
        $rssChannel = new RssChannel();
        $rssChannel->title = $rawChannel->simplePieInstance->get_title();
        $rssChannel->description = $rawChannel->simplePieInstance->get_description();
        $rssChannel->link = $rawChannel->simplePieInstance->get_link();
        $rssChannel->pub_date = $rawChannel->getDateTimeFromDataOf('pubDate');
        $rssChannel->last_build_date = $rawChannel->getDateTimeFromDataOf('lastBuildDate');
        $rssChannel->image = $rawChannel->simplePieInstance->get_image_url();
        return $rssChannel;
    }
}
