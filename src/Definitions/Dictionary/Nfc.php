<?php
/**
 * Created by PhpStorm.
 * User: Jean Rumeau
 * Date: 13/09/2017
 * Time: 23:29
 */

namespace Thenextweb\Definitions\Dictionary;

use Illuminate\Support\Fluent;

class Nfc extends Fluent
{
    public function __construct($message, $encryptionPublicKey = '')
    {
        $this->attributes['message'] = $message;
        if (!empty($encryptionPublicKey)) {
            $this->attributes['encryptionPublicKey'] = $encryptionPublicKey;
        }

        return $this;
    }

    public function setMessage($message)
    {
        $this->attributes['message'] = $message;

        return $this;
    }

    public function setEncryptionPublicKey($encryptionPublicKey)
    {
        $this->attributes['encryptionPublicKey'] = $encryptionPublicKey;

        return $this;
    }
}
