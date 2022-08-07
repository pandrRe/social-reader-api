<?php

namespace App\Services;

use Carbon\Traits\Date;
use DateTime;
use SimplePie\SimplePie;

class RawChannel
{
    public SimplePie $simplePieInstance;
    private bool $isLoaded;

    public function __construct(SimplePie $simplePieInstance)
    {
        $this->simplePieInstance = $simplePieInstance;
        $this->isLoaded = $this->simplePieInstance->init();
    }

    public function isLoaded() {
        return $this->isLoaded;
    }

    public function getChecksum(){
        $rawData = $this->simplePieInstance->get_raw_data();
        if ($rawData) {
            return md5($rawData);
        }
        else {
            return null;
        }
    }

    public function isRss() {
        $type = $this->simplePieInstance->get_type();
        return $type <= SIMPLEPIE_TYPE_RSS_ALL && $type > SIMPLEPIE_TYPE_NONE;
    }

    public function isAtom() {
        $type = $this->simplePieInstance->get_type();
        return $type <= SIMPLEPIE_TYPE_ATOM_ALL && $type > SIMPLEPIE_TYPE_RSS_ALL;
    }

    public function getTagOcurrences($tagName) {
        if ($this->isRss()) {
            return $this->simplePieInstance->get_channel_tags('', $tagName);
        }
        else if ($this->isAtom()) {
            return $this->simplePieInstance->get_feed_tags(SIMPLEPIE_NAMESPACE_ATOM_10, $tagName);
        }
        else {
            return null;
        }
    }

    public function getDataOf($tagName) {
        $tagOccurrences = $this->getTagOcurrences($tagName);
        if ($tagOccurrences) {
            return $tagOccurrences[0]['data'];
        }
        else {
            return null;
        }
    }

    public function getDateFormat() {
        if ($this->isRss()) {
            return DATE_RSS;
        }
        else if ($this->isAtom()) {
            return DATE_ATOM;
        }
        else {
            return null;
        }
    }

    public function getDateTimeFromDataOf($tagName): DateTime|null {
        $data = $this->getDataOf($tagName);
        $dateFormat = $this->getDateFormat();
        if ($data && $dateFormat) {
            $dateTime = DateTime::createFromFormat($dateFormat, $data);
            if ($dateTime instanceof DateTime) {
                return $dateTime;
            }
        }
        return null;
    }
}
