<?php

namespace basar911\phpPassport;

// 加密/解密
use basar911\phpPassport\handler\HandlerInterface;
use basar911\phpPassport\signHandler\DefaultSignHandler;
use basar911\phpPassport\signHandler\SignHandlerInterface;
use Exception;
use Throwable;

class Passport
{
    private $salt = '';  // 签名加盐
    private $expire = 10;  // 过期时间（秒）
    private $key = '';  // 签名私钥
    private $encrypt_key = '';  // 数据加解密私钥

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var SignHandlerInterface
     */
    private $sign_handler;

    public function config(array $config)
    {
        $this->salt   = $config['salt'];
        $this->expire = $config['expire'];
        $this->key    = $config['sign_key'];
        $this->encrypt_key = $config['crypt_key'] ?? $config['key'];

        return $this;
    }

    /**
     * @param HandlerInterface $handler
     * @param SignHandlerInterface|null $sign_handler
     * @return $this
     */
    public function handler(HandlerInterface $handler, $sign_handler = null)
    {
        $this->handler = $handler;
        $this->sign_handler = $sign_handler ?: (new DefaultSignHandler());

        return $this;
    }

    /**
     * 获取签名数据
     * @param array $params
     * @return array
     */
    public function encrypt(array $params)
    {
        $rand_str    = self::randStr(20);
        $timestamp   = $this->sign_handler->get_timestamp();
        $encrypt_str = $this->handler->encrypt(json_encode($params), $this->encrypt_key);
        $sign        = $this->get_sign($encrypt_str, $rand_str, $timestamp);

        return compact('sign', 'encrypt_str', 'rand_str', 'timestamp');
    }

    /**
     * 解析签名数据
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function decrypt(array $data)
    {
        ksort($data);
        list($encrypt_str, $rand_str, $sign, $timestamp) = array_values($data);

        if (!$this->sign_verify($sign, $encrypt_str, $rand_str, (int)$timestamp)) throw new Exception('签名错误');

        return json_decode($this->handler->decrypt($encrypt_str, $this->encrypt_key), true);
    }

    /**
     * 验签
     * @param $sign
     * @param $encrypt_str
     * @param $rand_str
     * @param int $timestamp 必须是int类型，否则报错
     * @return bool
     * @throws Exception|Throwable
     */
    private function sign_verify($sign, $encrypt_str, $rand_str, int $timestamp)
    {
        return $this->sign_handler->sign_verify($sign, $encrypt_str, $rand_str, $timestamp, $this->key, $this->expire, $this->salt);
    }

    /**
     * 计算签名
     * @param $encrypt_str
     * @param $rand_str
     * @param $timestamp
     * @return string
     */
    private function get_sign($encrypt_str, $rand_str, $timestamp)
    {
        return $this->sign_handler->get_sign($encrypt_str, $rand_str, $timestamp, $this->key, $this->salt);
    }

    /**
     * 产生随机字串，可用来自动生成密码
     * 默认长度6位 字母和数字混合 支持中文
     * @param  string  $len  长度
     * @param  string  $type  字串类型
     * 0 字母 1 数字 其它 混合
     * @param  string  $addChars  额外字符
     * @return string
     */
    private function randStr($len = 6, $type = '', $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz'.$addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
                break;
        }
        if ($len > 10) {//位数过长重复字符串一定次数
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }

        $chars = str_shuffle($chars);
        return substr($chars, 0, $len);
    }
}
