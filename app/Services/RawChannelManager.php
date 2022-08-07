<?php

namespace App\Services;

class RawChannelManager {
    public function createRawChannel($feedUrl): RawChannelDescriptor
    {
        return new RawChannelDescriptor($feedUrl);
    }
}
