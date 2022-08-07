<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelSubscription extends Model
{
    use HasFactory;

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function fromChannelAndUser(Channel $channel, User $user) {
        $channelSubscription = new ChannelSubscription();
        $channelSubscription->channel()->associate($channel);
        $channelSubscription->user()->associate($user);
        $channelSubscription->save();
        return $channelSubscription;
    }
}
