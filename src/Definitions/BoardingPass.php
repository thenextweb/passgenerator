<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 10:31
 */

namespace Thenextweb\Definitions;

use InvalidArgumentException;

class BoardingPass extends AbstractDefinition
{
    const TRANSIT_TYPE_AIR = 'PKTransitTypeAir';
    const TRANSIT_TYPE_BOAT = 'PKTransitTypeBoat';
    const TRANSIT_TYPE_BUS = 'PKTransitTypeBus';
    const TRANSIT_TYPE_GENERIC = 'PKTransitTypeGeneric';
    const TRANSIT_TYPE_TRAIN = 'PKTransitTypeTrain';

    /** @var array<string> */
    private $validTransitTypes = [
        self::TRANSIT_TYPE_AIR,
        self::TRANSIT_TYPE_BOAT,
        self::TRANSIT_TYPE_BUS,
        self::TRANSIT_TYPE_GENERIC,
        self::TRANSIT_TYPE_TRAIN,
    ];

    protected $style = 'boardingPass';

    public function __construct(array $attributes)
    {
        if (isset($attributes['transitType']) && !in_array($attributes['transitType'], $this->validTransitTypes)) {
            throw new InvalidArgumentException("Invalid Transit Type: {$attributes['transitType']}");
        }
    }

    /**
     * Type of transit. Must be one of the class constants.
     *
     * @param string $transitType
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setTransitType(string $transitType) : self
    {
        if (!in_array($transitType, $this->validTransitTypes)) {
            throw new InvalidArgumentException("Invalid Transit Type: $transitType");
        }
        $this->attributes['transitType'] = $transitType;

        return $this;
    }

    /**
     * Identifier used to group related passes. If a grouping identifier is
     * specified, passes with the same style, pass type identifier, and
     * grouping identifier are displayed as a group. Otherwise, passes are
     * grouped automatically.
     *
     * Use this to group passes that are tightly related, such as the boarding
     * passes for different connections of the same trip.
     *
     * Available in iOS 7.0.
     *
     * @param string $groupingIdentifier
     * @return self
     */
    public function setGroupingIdentifier(string $groupingIdentifier) : self
    {
        $this->attributes['groupingIdentifier'] = $groupingIdentifier;

        return $this;
    }
}
