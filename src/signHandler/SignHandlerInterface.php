<?php

namespace basar911\phpPassport\signHandler;

use Exception;

interface SignHandlerInterface
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
    public function get_sign($encrypt_str, $rand_str, $timestamp, string $key, string $salt = ''): string;

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
    public function sign_verify($sign, $encrypt_str, $rand_str, $timestamp, $key, $expire, string $salt = ''): bool;

    /**
     * 获取时间戳
     * @return int
     */
    public function get_timestamp(): int;
}