<?php

namespace App\Services;

use SimplePie\SimplePie;

class RawChannelDescriptor
{
    public SimplePie $simplePieInstance;

    public function __construct($feedUrl)
    {
        $this->simplePieInstance = new SimplePie();
        $this->simplePieInstance->set_feed_url($feedUrl);
        $this->simplePieInstance->enable_cache(false);
    }

    public function read(): RawChannel
    {
        return new RawChannel($this->simplePieInstance);
    }
}
