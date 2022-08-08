<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public static function findByIdAndUser(int $id, User $user) {
        return ChannelSubscription::query()
            ->where('id', $id)
            ->whereHas('user', function (Builder $query) use ($user) {
                $query->where()
            });
    }
}
