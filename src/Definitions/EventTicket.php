<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 10:31
 */

namespace Thenextweb\Definitions;

class EventTicket extends AbstractDefinition
{
    protected $style = 'eventTicket';

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
