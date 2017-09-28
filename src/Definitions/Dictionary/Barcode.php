<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 13/09/2017
 * Time: 23:17
 */

namespace Thenextweb\Definitions\Dictionary;

use Illuminate\Support\Fluent;

class Barcode extends Fluent
{
    const FORMAT_QR = 'PKBarcodeFormatQR';
    const FORMAT_PDF417 = 'PKBarcodeFormatPDF417';
    const FORMAT_AZTEX = 'PKBarcodeFormatAztec';
    const FORMAT_CODE128 = 'PKBarcodeFormatCode128';

    /**
     * Barcode constructor.
     * @param array|object $message
     * @param $format
     * @param string $messageEncoding
     */
    public function __construct($message, $format, $messageEncoding = 'iso-8859-1')
    {
        $this->attributes['message'] = $message;
        $this->attributes['format'] = $format;
        $this->attributes['messageEncoding'] = 'iso-8859-1';

        return $this;
    }

    /**
     * @param $altText
     * @return $this
     */
    public function setAltText($altText)
    {
        $this->attributes['altText'] = $altText;

        return $this;
    }

    /**
     * @param $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->attributes['format'] = $format;

        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->attributes['message'] = $message;

        return $this;
    }

    /**
     * @param $messageEncoding
     * @return $this
     */
    public function setMessageEncoding($messageEncoding)
    {
        $this->attributes['messageEncoding'] = $messageEncoding;

        return $this;
    }
}
