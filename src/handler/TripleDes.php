<?php

namespace basar911\phpPassport\handler;

// 3des加解密
class TripleDes implements HandlerInterface
{
    /**
     * 3Des加密
     *
     * @param $str
     * @return
     */
    public function encrypt($str, $key): string
    {
        $data = $this->padding($str);
        $output = openssl_encrypt(
            $data,
            'DES-EDE3-CBC',
            hex2bin($key),
            OPENSSL_NO_PADDING,
            pack('a8', '')
        );

        return strtoupper(bin2hex($output));
    }

    /**
     * 填充0
     *
     * @access  private
     * @param  string  $data 数据
     * @return  string
     */
    private function padding($data){
        $block_size = openssl_cipher_iv_length('DES-EDE3-CBC');
        if (strlen($data) % $block_size) {
            $padding_char = $block_size - (strlen($data) % $block_size);
            $data .= str_repeat(chr(0), $padding_char);
        }
        return $data;
    }

    public function decrypt($str, $key): string
    {
        $encrypted = hex2bin($str);
        $output = openssl_decrypt(
            $encrypted,
            'DES-EDE3-CBC',
            hex2bin($key),
            OPENSSL_NO_PADDING,
            pack('a8', '')
        );

        return $this->unpadding($output);
    }

    /**
     * 去除填充的0
     *
     * @access private
     * @param string $data 解密后的数据
     * @return string
     */
    private function unpadding($data){
        return rtrim($data, "\0");
    }
}