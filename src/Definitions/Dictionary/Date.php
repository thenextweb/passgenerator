<?php

namespace Thenextweb\Definitions\Dictionary;

use Carbon\Carbon;
use InvalidArgumentException;

class Date extends Field
{

    /**
     * Either specify both a date style and a time style, or neither.
     */
    const STYLE_NONE = 'PKDateStyleNone';
    const STYLE_SHORT = 'PKDateStyleShort';
    const STYLE_MEDIUM = 'PKDateStyleMedium';
    const STYLE_LONG = 'PKDateStyleLong';
    const STYLE_FULL = 'PKDateStyleFull';
    /** @var array<string> */
    private $validStyles = [self::STYLE_NONE, self::STYLE_SHORT, self::STYLE_MEDIUM, self::STYLE_LONG, self::STYLE_FULL];
    /**
     * Style of date to display. Must be one of the styles from the class.
     *
     * @param string $dateStyle
     * @return self
     */
    public function setDateStyle(string $dateStyle)
    {
        if (!in_array($dateStyle, $this->validStyles)) {
            throw new InvalidArgumentException('Invalid barcode format');
        }

        $this->attributes['dateStyle'] = $dateStyle;

        return $this;
    }

    /**
     * Always display the time and date in the given time zone, not in the user’s
     * current time zone.
     * The default value is false.
     *
     * The format for a date and time always requires a time zone, even if it will be
     * ignored. For backward compatibility with iOS 6, provide an appropriate time zone,
     * so that the information is displayed meaningfully even without ignoring time zones.
     *
     * This key does not affect how relevance is calculated.
     *
     * Available in iOS 7.0.
     *
     * @param bool $flag
     * @return self
     */
    public function setIgnoresTimeZone(bool $flag)
    {
        $this->attributes['ignoresTimeZone'] = $flag;

        return $this;
    }

    /**
     * If true, the label’s value is displayed as a relative date; otherwise,
     * it is displayed as an absolute date. The default value is false.
     *
     * This key does not affect how relevance is calculated.
     *
     * @param bool $flag
     * @return self
     */
    public function setIsRelative(bool $flag)
    {
        $this->attributes['isRelative'] = $flag;

        return $this;
    }

    /**
     * Style of time to display. Must be one of the styles from the class.
     *
     * @param string $timeStyle
     * @return self
     */
    public function setTimeStyle($timeStyle)
    {
        if (!in_array($timeStyle, $this->validStyles)) {
            throw new InvalidArgumentException('Invalid barcode format');
        }

        $this->attributes['timeStyle'] = $timeStyle;

        return $this;
    }

    /**
     * Make sure when converting to array Carbon values are
     * converted to W3C String
     */
    public function toArray() : array
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
