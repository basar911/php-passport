<?php

namespace basar911\phpPassport\handler;

class StreamHandler implements HandlerInterface
{
    public function encrypt($data, $key = '')
    {
        srand((double)microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr         = 0;
        $tmp         = '';
        $len         = strlen($data);

        for ($i = 0; $i < $len; $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($data[$i] ^ $encrypt_key[$ctr++]);
        }
        return urlencode(base64_encode($this->passport_key($tmp, $key)));
    }

    public function decrypt($data, $key)
    {
        $data = $this->passport_key(base64_decode(urldecode($data)), $key);
        $tmp = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $md5 = $data[$i];
            $tmp .= $data[++$i] ^ $md5;
        }
        return $tmp;
    }

    private function passport_key($data, $encrypt_key)
    {
        $encrypt_key = md5($encrypt_key);
        $ctr         = 0;
        $tmp         = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $data[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }
}