# Usage

1. 必须保证与 sso 同域；
2. 将 libsso 文件夹放置在需要的项目中；
3. 复制并修改 config.php.sample，执行 initialize.sh；
4. 使用方法如下。

```php
<?php
require_once './path/to/libsso.class.php';
print_r(SSO::getUser());
```

一个可能的输出结果如下：

```text
Array
(
    [uid] => 1
    [username] => Click_04
    [email] => rex@rexskz.info
    [auth_ded] => 161320213
)
```

目前可以使用的函数有：

```php
// 获取用户信息,$uid缺省则获取当前登录用户的信息
getUserInfo([$uid])
// 通过 openid 获取用户
getUserByOpenid($openid)
// 获取用户马甲列表
getUserRepeats()
// 加密解密
ssoEncrypt($str)
ssoDecrypt($str)
// 为前端输出 public key
getPubkeyForJs()
// 生成登录界面的链接
generateLoginUrl()
// 跳至登录界面，登录后跳回现有页面
gotoLogin()
// 通过 学号 获取用户的uid和姓名信息
getUserByDed($stuid)
```
