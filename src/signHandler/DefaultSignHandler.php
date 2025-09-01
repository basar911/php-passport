<?php

namespace basar911\phpPassport\signHandler;

use Exception;

// 默认签名算法
class DefaultSignHandler implements SignHandlerInterface
{

    /**
     * 计算签名
     * @param $encrypt_str
     * @param $rand_str
     * @param $timestamp
     * @param string $key
     * @param string $salt
     * @return string
     */
    public function get_sign($encrypt_str, $rand_str, $timestamp, string $key, string $salt= ''):string
    {
        $data = [
            'key'       => $key,
            'randstr'   => $rand_str,
            'params'    => $encrypt_str,
            'timestamp' => (int)$timestamp
        ];

        ksort($data);
        //待签名字符串
        $str_sign = json_encode($data);
        $str_sign = str_replace('\/', '/', $str_sign);
        return md5(md5($str_sign . $salt));
    }

    /**
     * 验签
     * @param $sign
     * @param $encrypt_str
     * @param $rand_str
     * @param $timestamp
     * @param $key
     * @param $expire
     * @param string $salt
     * @return bool
     * @throws Exception
     */
    public function sign_verify($sign, $encrypt_str, $rand_str, $timestamp, $key, $expire, string $salt = ''): bool
    {
        if (time() - $timestamp > $expire) throw new Exception('签名已过期');

        if (!hash_equals($sign, $this->get_sign($encrypt_str, $rand_str, (int)$timestamp, $key, $salt))) return false;

        return true;
    }

    /**
     * 获取时间戳
     * @return int
     */
    public function get_timestamp(): int
    {
        return time();
    }
}