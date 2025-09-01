<?php

namespace basar911\phpPassport\signHandler;

use Exception;

class SignV2Handler implements SignHandlerInterface
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
    public function get_sign($encrypt_str, $rand_str, $timestamp, string $key, string $salt = ''): string
    {
        // md5(data={参数data加密后的数据}&nonce={随机字符串}&timestamp={13位时间戳}&key={secretkey秘钥})
        return md5("data=$encrypt_str&nonce=$rand_str&timestamp=$timestamp&key=$key");
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
    public function sign_verify($sign, $encrypt_str, $rand_str, $timestamp, $key, $expire, $salt = ''): bool
    {
        if ($this->get_micro_time() - $timestamp > $expire) throw new Exception('签名已过期');

        if (!hash_equals($sign, $this->get_sign($encrypt_str, $rand_str, $timestamp, $key, $salt))) return false;

        return true;
    }

    /**
     * 获取时间戳
     * @return int
     */
    public function get_timestamp(): int
    {
        return $this->get_micro_time();
    }

    private function get_micro_time()
    {
        // 获取当前的微秒时间戳
        $microtime = microtime(true);

        return (int)bcmul($microtime, 1000);
    }
}