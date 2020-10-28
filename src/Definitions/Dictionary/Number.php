<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 11:46
 */

namespace Thenextweb\Definitions\Dictionary;

use InvalidArgumentException;

class Number extends Field
{
    const STYLE_DECIMAL = 'PKNumberStyleDecimal';
    const STYLE_PERCENT = 'PKNumberStylePercent';
    const STYLE_SCIENTIFIC = 'PKNumberStyleScientific';
    const STYLE_SPELLOUT = 'PKNumberStyleSpellOut';

    /** @var array<string> */
    private $validNumberStyles = [
        self::STYLE_DECIMAL,
        self::STYLE_PERCENT,
        self::STYLE_SCIENTIFIC,
        self::STYLE_SPELLOUT,
    ];

    /**
     * ISO 4217 currency code for the field’s value.
     * @param string $currencyCode
     * @return self
     */
    public function setCurrencyCode(string $currencyCode) : self
    {
        $this->attributes['currencyCode'] = $currencyCode;

        return $this;
    }

    /**
     * Style of number to display. Must be one of the class constants.
     * @param string $numberStyle
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setNumberStyle(string $numberStyle) : self
    {
        if (!in_array($numberStyle, $this->validNumberStyles)) {
            throw new InvalidArgumentException("Invalid number style: $numberStyle");
        }

        $this->attributes['numberStyle'] = $numberStyle;

        return $this;
    }
}
