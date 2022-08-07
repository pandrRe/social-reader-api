<?php

namespace App\Http\Controllers;

use App\Models\ChannelSubscription;
use App\Services\RawChannelManager;
use App\Models\Channel;
use Illuminate\Http\Request;
use SimplePie\SimplePie;

class ChannelSubscriptionController extends Controller
{
    protected RawChannelManager $rawChannelManager;

    public function __construct(RawChannelManager $rawChannelManager) {
        $this->rawChannelManager = $rawChannelManager;
    }

    public function subscribe(Request $request) {
        $submittedSubscription = $request->validate([
            'xml_source' => 'required',
        ]);
        $subscribingUser = $request->user();

        $rawChannel = $this->rawChannelManager
            ->createRawChannel($submittedSubscription['xml_source'])
            ->read();

        $channel = Channel::fromRawChannel($rawChannel);
        return ChannelSubscription::fromChannelAndUser($channel, $subscribingUser);
    }
}
