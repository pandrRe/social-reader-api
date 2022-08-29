<?php

namespace App\Models;

use App\Services\RawChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtomFeed extends Model
{
    use HasFactory;

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function entries() {
        return $this->hasMany(AtomEntry::class);
    }

    public static function makeFromRawChannel(RawChannel $rawChannel) {
        $atomFeed = new AtomFeed();
        $atomFeed->atom_id = $rawChannel->getDataOf('id');
        $atomFeed->title = $rawChannel->simplePieInstance->get_title();
        $atomFeed->updated = $rawChannel->getDateTimeFromDataOf('updated');
        $atomFeed->self_link = $rawChannel->simplePieInstance->get_link(0, 'self');
        $atomFeed->alternate_link = $rawChannel->simplePieInstance->get_link();
        $atomFeed->subtitle = $rawChannel->getDataOf('subtitle');
        $atomFeed->icon = $rawChannel->getDataOf('icon');
        $atomFeed->logo = $rawChannel->getDataOf('logo');
        return $atomFeed;
    }
}
