<?php

namespace basar911\phpPassport\signHandler;

class SignV3Handler implements SignHandlerInterface
{

    public function get_sign($encrypt_str, $rand_str, $timestamp, string $key, string $salt = ''): string
    {

        if (is_object($encrypt_str)) { //对象转数组
            $encrypt_str = json_decode(json_encode($encrypt_str), true);
        }

        if (!empty($key)) {
            $encrypt_str['sign_key'] = $key;
        }

        return $this->generateSign($encrypt_str);
    }

    public function sign_verify($sign, $encrypt_str, $rand_str, $timestamp, $key, $expire, string $salt = ''): bool
    {
        $data = $encrypt_str;//接收所有post的数据
        unset($data['sign']);//删除掉sign字段
        $data['sign_key'] = $key;
        $mysign = $this->generateSign($data);

        if (!hash_equals($mysign, $sign)) return false;

        return true;
    }

    public function get_timestamp(): int
    {
        // TODO: Implement get_timestamp() method.
        return time();
    }

    private function generateSign(array $data)
    {
        ksort($data);
        $formatData = [];

        foreach ($data as $k => $v) {
            if (is_array($v) || is_object($v)) {
                $v = json_encode($v,JSON_UNESCAPED_UNICODE);
            }
            if ((!empty($v) || (string)$v === '0') && $k != 'sign') {
                $formatData[] = "$k=$v";
            }
        }

        $signStr = implode('&', $formatData);

        return md5($signStr);
    }
}