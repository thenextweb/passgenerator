<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 14/09/2017
 * Time: 11:46
 */

namespace Thenextweb\Definitions\Dictionary;

class Number extends Field
{
    const STYLE_DECIMAL = 'PKNumberStyleDecimal';
    const STYLE_PERCENT = 'PKNumberStylePercent';
    const STYLE_SCIENTIFIC = 'PKNumberStyleScientific';
    const STYLE_SPELLOUT = 'PKNumberStyleSpellOut';

    /**
     * @param $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->attributes['currencyCode'] = $currencyCode;

        return $this;
    }

    /**
     * @param $numberStyle
     * @return $this
     */
    public function setNumberStyle($numberStyle)
    {
        $this->attributes['numberStyle'] = $numberStyle;

        return $this;
    }
}
