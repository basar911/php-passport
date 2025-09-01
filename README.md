# php-unit

#### 介绍
php常用加解密和签名生成及验证
常用加解密类型：AES/STREAM/SM4/3-DES

#### 签名加密 + 验签解密 例子
```php
<?php
require __DIR__ . '/vendor/autoload.php';

// 选择加解密处理类
$handler = \basar911\phpPassport\handler\SM4Handler::class;

// 选择签名处理类
$sign_handler = \basar911\phpPassport\signHandler\FinanceSignHandler::class;

// 加密数组数据，可任意设置
$data = ['handler' => $handler, 'sign' => $sign_handler, 'a' => 1, 'b' => [['c' => 'as3423'], ['c' => 'sdy54ty45']]];

$passport = (new \basar911\phpPassport\Passport())
                ->handler(new $handler, new $sign_handler)
                ->config([
                    'salt'      => 'ss08dus',  // 签名加盐
                    'expire'    => 100,  // 过期时间(秒)
                    'sign_key'  => 'kQwIOrYvnXmSDkwEiFngrKidMcdrgKor',  // 签名密钥
                    'crypt_key' => '9b311c7a94d0704a7f29e53707b90420dababad398f7df23',  // 加解密密钥， 注意：SM4密钥长度至少15，TripleDes密钥长度必须为偶数
                ]);

// 加密，返回格式[sign":"签名","encrypt_str":"加密结果","rand_str":"随机字符串","timestamp":时间戳]
$encrypt = $passport->encrypt($data);

// 解密+验签
$decrypt = $passport->decrypt($encrypt);

echo json_encode(compact('encrypt', 'decrypt'), 384);
?>
```

若只想加解密，不需要签名，可直接引用加解密处理类，不再注入到Passport中引用
```php
<?php
require __DIR__ . '/vendor/autoload.php';

// 选择加解密处理类
$handler = \basar911\phpPassport\handler\SM4Handler::class;

// 加密数组数据，可任意设置
$data = ['handler' => $handler, 'sign' => $sign_handler, 'a' => 1, 'b' => [['c' => 'as3423'], ['c' => 'sdy54ty45']]];
$key = '9b311c7a94d0704a7f29e53707b90420dababad398f7df23';  // 加解密密钥， 注意：SM4密钥长度至少15，TripleDes密钥长度必须为偶数

// 加密
$encrypt = $handler->encrypt($data, $key);

// 解密
$decrypt = $handler->decrypt($encrypt, $key);

echo json_encode(compact('encrypt', 'decrypt'), 384);
?>
```
当然，你也可以自定义你自己的加解密类和签名处理类
只要分别实现\basar911\phpPassport\handler\HandlerInterface
和\basar911\phpPassport\signHandler\SignHandlerInterface
再通过Passport->handler()注入引用

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request


#### 特技

1.  使用 Readme\_XXX.md 来支持不同的语言，例如 Readme\_en.md, Readme\_zh.md
2.  Gitee 官方博客 [blog.gitee.com](https://blog.gitee.com)
3.  你可以 [https://gitee.com/explore](https://gitee.com/explore) 这个地址来了解 Gitee 上的优秀开源项目
4.  [GVP](https://gitee.com/gvp) 全称是 Gitee 最有价值开源项目，是综合评定出的优秀开源项目
5.  Gitee 官方提供的使用手册 [https://gitee.com/help](https://gitee.com/help)
6.  Gitee 封面人物是一档用来展示 Gitee 会员风采的栏目 [https://gitee.com/gitee-stars/](https://gitee.com/gitee-stars/)
