<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class RssItem extends Model
{
    use HasFactory;

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    protected $guarded = [];

    public static function getData(\SimplePie_Item $rawItem, $tagName) {
        $ocurrences = $rawItem->get_item_tags('', $tagName);
        if ($ocurrences) {
            return $ocurrences[0]['data'];
        }
        return null;
    }

    public static function getDateTimeFromData(\SimplePie_Item $rawItem, $tagName): DateTime|null {
        $data = self::getData($rawItem, $tagName);
        if ($data) {
            $treatedData = str_replace('.000000000', '', $data);
            $dateTime = DateTime::createFromFormat(DATE_RSS, $treatedData);
            if ($dateTime instanceof DateTime) {
                return $dateTime;
            }
        }
        return null;
    }

    public static function upsertableFromRawItem(\SimplePie_Item $rawItem, Channel $channel) {
        $link = self::getData($rawItem, 'link');
        $guid = self::getData($rawItem, 'guid');
        $comments = self::getData($rawItem, 'comments');
        $source = self::getData($rawItem, 'source');
        $pubDate = self::getDateTimeFromData($rawItem, 'pubDate');

        Log::info($rawItem->get_title());

        if ($guid) {
            return [
                ['guid' => $guid, 'channel_id' => $channel->id],
                [
                    'title' => $rawItem->get_title(),
                    'description' => $rawItem->get_description(),
                    'content' => $rawItem->get_content(true),
                    'link' => $link,
                    'author' => $rawItem->get_author()? $rawItem->get_author()->get_name() : null,
                    'comments' => $comments,
                    'source' => $source,
                    'pub_date' => $pubDate,
                ]
            ];
        }
        else if ($link) {
            return [
                ['link' => $link, 'channel_id' => $channel->id],
                [
                    'title' => $rawItem->get_title(),
                    'description' => $rawItem->get_description(),
                    'content' => $rawItem->get_content(true),
                    'author' => $rawItem->get_author()? $rawItem->get_author()->get_name() : null,
                    'comments' => $comments,
                    'source' => $source,
                    'pub_date' => $pubDate,
                ]
            ];
        }
        return [
            ['title' => $rawItem->get_title(), 'description' => $rawItem->get_description(), 'channel_id' => $channel->id],
            [
                'content' => $rawItem->get_content(true),
                'author' => $rawItem->get_author()? $rawItem->get_author()->get_name() : null,
                'comments' => $comments,
                'source' => $source,
                'pub_date' => $pubDate,
            ]
        ];
    }
}
