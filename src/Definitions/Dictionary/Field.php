<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 9:36
 */

namespace Thenextweb\Definitions\Dictionary;

use Illuminate\Support\Fluent;

class Field extends Fluent
{
    const DETECTOR_TYPE_PHONE = 'PKDataDetectorTypePhoneNumber';
    const DETECTOR_TYPE_LINK = 'PKDataDetectorTypeLink';
    const DETECTOR_TYPE_ADDRESS = 'PKDataDetectorTypeAddress';
    const DETECTOR_TYPE_CALENDAR_EVENT = 'PKDataDetectorTypeCalendarEvent';

    const TEXT_ALIGNMENT_LEFT = 'PKTextAlignmentLeft';
    const TEXT_ALIGNMENT_CENTER = 'PKTextAlignmentCenter';
    const TEXT_ALIGNMENT_RIGHT = 'PKTextAlignmentRight';
    const TEXT_ALIGNMENT_NATURAL = 'PKTextAlignmentNatural';

    /**
     * Field constructor.
     * @param $key
     * @param $value
     * @param $attributes
     */
    public function __construct($key, $value, $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['key'] = $key;
        $this->attributes['value'] = $value;

        return $this;
    }

    /**
     * @param $attributedValue
     * @return $this
     */
    public function setAttributedValue($attributedValue)
    {
        $this->attributes['attributedValue'] = $attributedValue;

        return $this;
    }

    /**
     * @param $changeMessage
     * @return $this
     */
    public function setChangeMessage($changeMessage)
    {
        $this->attributes['changeMessage'] = $changeMessage;

        return $this;
    }

    /**
     * @param $dataDetectorTypes
     * @return $this
     */
    public function setDataDetectorTypes($dataDetectorTypes)
    {
        $this->attributes['dataDetectorTypes'] = $dataDetectorTypes;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->attributes['key'] = $key;

        return $this;
    }

    /**
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->attributes['label'] = $label;

        return $this;
    }

    /**
     * @param $textAlignment
     * @return $this
     */
    public function setTextAlignment($textAlignment)
    {
        $this->attributes['textAlignment'] = $textAlignment;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->attributes['value'] = $value;

        return $this;
    }
}
