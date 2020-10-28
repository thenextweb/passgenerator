<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 9:36
 */

namespace Thenextweb\Definitions\Dictionary;

use Illuminate\Support\Fluent;
use InvalidArgumentException;

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

    /** @var array<string> */
    private $validDetectors = [
        self::DETECTOR_TYPE_PHONE,
        self::DETECTOR_TYPE_LINK,
        self::DETECTOR_TYPE_ADDRESS,
        self::DETECTOR_TYPE_CALENDAR_EVENT,
    ];

    /** @var array<string> */
    private $validAlignments = [
        self::TEXT_ALIGNMENT_LEFT,
        self::TEXT_ALIGNMENT_CENTER,
        self::TEXT_ALIGNMENT_RIGHT,
        self::TEXT_ALIGNMENT_NATURAL,
    ];

    /**
     * Field constructor.
     * @param string $key The key must be unique within the scope of
     *                    the entire pass. For example, “departure-gate.”
     * @param string $value Value of the field, for example, 42.
     * @param array $attributes Other attributes for the field.
     */
    public function __construct(string $key, string $value, array $attributes = [])
    {
        if (isset($attributes['dataDetectorTypes'])) {
            $this->validateDetectors($attributes['dataDetectorTypes']);
        }

        if (isset($attributes['textAlignment'])) {
            $this->validateAlignment($attributes['textAlignment']);
        }

        parent::__construct(array_merge($attributes, ['key' => $key, 'value' => $value]));
    }

    /**
     * Attributed value of the field.
     *
     * The value may contain HTML markup for links.
     * Only the <a> tag and its href attribute are supported.
     * For example, the following is key-value pair specifies
     * a link with the text “Edit my profile”:
     * "attributedValue": "<a href='http://example.com/customers/123'>Edit my profile</a>"
     *
     * This key’s value overrides the text specified by the value key.
     *
     * Available in iOS 7.0.
     *
     * @param string $attributedValue
     * @return self
     */
    public function setAttributedValue(string $attributedValue) : self
    {
        $this->attributes['attributedValue'] = $attributedValue;

        return $this;
    }

    /**
     * Format string for the alert text that is displayed when the pass is updated.
     * The format string must contain the escape %@, which is replaced with the
     * field’s new value. For example, “Gate changed to %@.”
     *
     * If you don’t specify a change message, the user isn’t notified when the field changes.
     *
     * @param string $changeMessage
     * @return self
     */
    public function setChangeMessage(string $changeMessage) : self
    {
        $this->attributes['changeMessage'] = $changeMessage;

        return $this;
    }

    /**
     * Data detectors that are applied to the field’s value. Valid values are set
     * as constant in the class.
     * The default value is all data detectors. Provide an empty array to use no data
     * detectors.
     *
     * Data detectors are applied only to back fields.
     *
     * @param array $dataDetectorTypes
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setDataDetectorTypes(array $dataDetectorTypes) : self
    {
        $this->validateDetectors($dataDetectorTypes);

        $this->attributes['dataDetectorTypes'] = $dataDetectorTypes;
        return $this;
    }

    /**
     * The key must be unique within the scope of the entire pass.
     * For example, “departure-gate.”
     *
     * @param string $key
     * @return $this
     */
    public function setKey(string $key) : self
    {
        $this->attributes['key'] = $key;

        return $this;
    }

    /**
     * Label text for the field.
     * @param string $label
     * @return self
     */
    public function setLabel(string $label) : self
    {
        $this->attributes['label'] = $label;

        return $this;
    }

    /**
     * @param string $textAlignment
     * @return self
     */
    public function setTextAlignment(string $textAlignment) : self
    {
        $this->attributes['textAlignment'] = $textAlignment;

        return $this;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setValue(string $value) : self
    {
        $this->attributes['value'] = $value;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validateDetectors(array $detectors) : void
    {
        if (!empty($detectors)) {
            foreach ($detectors as $detector) {
                if (!in_array($detector, $this->validDetectors)) {
                    throw new InvalidArgumentException("Invalid detector found: $detector");
                }
            }
        }
    }

    /**
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validateAlignment(string $alignment) : void
    {
        if (!in_array($alignment, $this->validAlignments)) {
            throw new InvalidArgumentException("Invalid alignment: $alignment");
        }
    }
}
