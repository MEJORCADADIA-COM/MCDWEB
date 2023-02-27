<?php

class Encrypter
{
    private string $cipher = 'aes-128-cbc';
    private static array $cipherProps = ['size' => 16, 'aead' => false];

    public function __construct(private string $key)
    {
    }

    /**
     * @throws Exception
     */
    public static function generateKey(): string
    {
        return random_bytes(self::$cipherProps['size'] ?? 32);
    }

    public function encrypt($value, $serialize = true): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(strtolower($this->cipher)));

        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            strtolower($this->cipher), $this->key, 0, $iv, $tag
        );

        if ($value === false) {
            dd('Could not encrypt the data.', 30);
        }

        $iv = base64_encode($iv);
        $tag = base64_encode($tag ?? '');

        $mac = self::$cipherProps['aead']
            ? '' // For AEAD-algoritms, the tag / MAC is returned by openssl_encrypt...
            : $this->hash($iv, $value);

        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('Could not encrypt the data.', 43);
        }

        return base64_encode($json);
    }

    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $this->ensureTagIsValid(
            $tag = empty($payload['tag']) ? null : base64_decode($payload['tag'])
        );

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
            $payload['value'], strtolower($this->cipher), $this->key, 0, $iv, $tag ?? ''
        );

        if ($decrypted === false) {
            dd('Could not decrypt the data.', 67);
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    protected function hash($iv, $value): bool|string
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $this->validPayload($payload)) {
            dd('The payload is invalid.', 86);
        }

        if (! self::$cipherProps['aead'] && ! $this->validMac($payload)) {
            dd('The MAC is invalid.', 90);
        }

        return $payload;
    }

    protected function validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
            strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(strtolower($this->cipher));
    }

    protected function ensureTagIsValid($tag)
    {
        if (self::$cipherProps['aead'] && strlen($tag) !== 16) {
            dd('Could not decrypt the data.', 105);
        }

        if (! self::$cipherProps['aead'] && is_string($tag)) {
            dd('Unable to use tag because the cipher algorithm does not support AEAD.', 109);
        }
    }

    protected function validMac(array $payload)
    {
        return hash_equals(
            $this->hash($payload['iv'], $payload['value']), $payload['mac']
        );
    }

    public function getKey()
    {
        return $this->key;
    }
}