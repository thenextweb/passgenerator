<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 11:46
 */

namespace Thenextweb\Definitions\Dictionary;

use Carbon\Carbon;

class Date extends Field
{
    const STYLE_NONE = 'PKDateStyleNone';
    const STYLE_SHORT = 'PKDateStyleShort';
    const STYLE_MEDIUM = 'PKDateStyleMedium';
    const STYLE_LONG = 'PKDateStyleLong';
    const STYLE_FULL = 'PKDateStyleFull';

    /**
     * @param $dateStyle
     * @return $this
     */
    public function setDateStyle($dateStyle)
    {
        $this->attributes['dateStyle'] = $dateStyle;

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIgnoresTimeZone(bool $flag)
    {
        $this->attributes['ignoresTimeZone'] = $flag;

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsRelative(bool $flag)
    {
        $this->attributes['isRelative'] = $flag;

        return $this;
    }

    /**
     * @param $timeStyle
     * @return $this
     */
    public function setTimeStyle($timeStyle)
    {
        $this->attributes['timeStyle'] = $timeStyle;

        return $this;
    }

    /**
     *
     */
    public function toArray()
    {
        $data = [];
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof Carbon) {
                $data[$key] = $value->toW3cString();
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
