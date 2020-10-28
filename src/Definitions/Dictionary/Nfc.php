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
    /**
     * @param string $message The payload to be transmitted to the Apple Pay terminal.
     *                        Must be 64 bytes or less.
     *                        Messages longer than 64 bytes are truncated by the system.
     * @param string $encryptionPublicKey The public encryption key used by the
     *                                    Value Added Services protocol. Use a Base64 encoded X.509
     *                                    SubjectPublicKeyInfo structure containing a ECDH public key
     *                                    for group P256.
     */
    public function __construct(string $message, string $encryptionPublicKey = null)
    {
        $attributes = compact('message');
        if (!is_null($encryptionPublicKey)) {
            $attributes['encryptionPublicKey'] = $encryptionPublicKey;
        }
        parent::__construct($attributes);
    }

    /**
     * The payload to be transmitted to the Apple Pay terminal. Must be 64 bytes
     * or less. Messages longer than 64 bytes are truncated by the system.
     * @param string $message
     * @return self
     */
    public function setMessage(string $message) : self
    {
        $this->attributes['message'] = $message;

        return $this;
    }

    /**
     * The public encryption key used by the Value Added Services protocol.
     * Use a Base64 encoded X.509 SubjectPublicKeyInfo structure containing a
     * ECDH public key for group P256.
     *
     * @param string $encryptionPublicKey
     * @return self
     */
    public function setEncryptionPublicKey(string $encryptionPublicKey) : self
    {
        $this->attributes['encryptionPublicKey'] = $encryptionPublicKey;

        return $this;
    }
}
