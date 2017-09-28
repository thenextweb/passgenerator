<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 13/09/2017
 * Time: 22:24
 */

namespace Thenextweb\Definitions\Dictionary;

use Illuminate\Support\Fluent;

class Beacon extends Fluent
{
    /**
     * @param $major
     * @return $this
     */
    public function setMajor($major)
    {
        $this->attributes['major'] = $major;

        return $this;
    }

    /**
     * @param $minor
     * @return $this
     */
    public function setMinor($minor)
    {
        $this->attributes['minor'] = $minor;

        return $this;
    }

    /**
     * @param $proximityUUID
     * @return $this
     */
    public function setProximityUUID($proximityUUID)
    {
        $this->attributes['proximityUUID'] = $proximityUUID;

        return $this;
    }

    /**
     * @param $relevantText
     * @return $this
     */
    public function setRelevantText($relevantText)
    {
        $this->attributes['relevantText'] = $relevantText;

        return $this;
    }
}
