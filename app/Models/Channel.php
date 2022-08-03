<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    public function rssChannel() {
        return $this->hasOne(RssChannel::class);
    }

    public function atomFeed() {
        return $this->hasOne(AtomFeed::class);
    }
}
