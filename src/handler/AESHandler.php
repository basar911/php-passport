<?php

namespace basar911\phpPassport\handler;

class AESHandler implements HandlerInterface
{
    private $method;

    public function __construct($method = 'AES-256-CBC')
    {
        $this->method = $method;
    }

    public function encrypt($data, $key)
    {
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, $this->method, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encrypted);
    }

    public function decrypt($data, $key)
    {
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = substr($data, 0, $ivLength);
        $data = substr($data, $ivLength);

        return openssl_decrypt($data, $this->method, $key, OPENSSL_RAW_DATA, $iv);
    }
}