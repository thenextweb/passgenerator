<?php

namespace Thenextweb\Definitions\Dictionary;

use Illuminate\Support\Fluent;

class Beacon extends Fluent
{
    public function __construct(string $proximityUUID, int $minor = null, int $major = null, string $relevantText = null)
    {
        $data = compact('proximityUUID', 'minor', 'major', 'relevantText');
        $attributes = collect($data)->filter()->toArray();
        parent::__construct($attributes);
    }

    /**
     * Major identifier of a Bluetooth Low Energy location beacon.
     *
     * @param int $major 16-bit unsigned integer
     * @return self
     */
    public function setMajor(int $major)
    {
        $this->attributes['major'] = $major;

        return $this;
    }

    /**
     * Minor identifier of a Bluetooth Low Energy location beacon
     *
     * @param int $minor 16-bit unsigned integer
     * @return self
     */
    public function setMinor(int $minor)
    {
        $this->attributes['minor'] = $minor;

        return $this;
    }

    /**
     * Unique identifier of a Bluetooth Low Energy location beacon.
     *
     * @param string $proximityUUID
     * @return self
     */
    public function setProximityUUID(string $proximityUUID)
    {
        $this->attributes['proximityUUID'] = $proximityUUID;

        return $this;
    }

    /**
     * Text displayed on the lock screen when the pass is currently relevant.
     * For example, a description of the nearby location such as
     * “Store nearby on 1st and Main.”
     *
     * @param string $relevantText
     * @return self
     */
    public function setRelevantText(string $relevantText)
    {
        $this->attributes['relevantText'] = $relevantText;

        return $this;
    }
}
