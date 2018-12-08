### 要求
1. PHP >= 7.0
2. php protobuf扩展 >=3.5.0
3. yiisoft/yii2 >= 2.0.0


### 安装
```bash
composer require language/yii2-protobuf
```


### 配置
```php
return [
    ...
    'components' => [
            ...
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'inwyiHVV0KPon5UhGv6l0QYaWL4SC1ww',
            'parsers' => [
                'application/x-protobuf' => 'Language\Protobuf\ProtobufParser'
            ],
        ],
        'response' => [
            'formatters'=>[
                'protobuf' => [
                    'class'=>'Language\Protobuf\ProtobufResponseFormatter',
                    'hump'=>true, //默认开启字段名下划线转驼峰式， 例如 iphone_num 转为 IphoneNum
                ],
            ],
        ],
        ...
    ],
]

```

### HTTP Header
解析或格式化protobuf数据统一使用Content-Type的类型为application/x-protobuf


### protobuf消息注册
概述：默认已经注释为解析protobuf和封装定义结构的消息了，生成消息的方式参见proto文件夹下build.sh

使用建议：可以将proto文件夹，放到yii工程文件下，执行build.sh后生成PbApp和GPBMetadata文件夹，这样可以免去写自动加载方法并和扩展protobuf包名保持一致


使用消息管理类注释protobuf解析定义文件

```php
class BaseController extends Controller
{
    use ProtobufTrait;  //利用trait特性asProtobuf方法注入

    public function init()
    {
        parent::init();
        // 消息文件注册
        ProtobufManager::register(ROOT . '/proto/msg.proto.php'); //ROOT . '/proto/msg.proto.php' 为脚本生成的php消息，可注册多个
    }
}
```

### 获取Body参数和响应范例
```php
<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/12/1
 * Time: 下午9:10
 */

namespace frontend\controllers;


use Language\Protobuf\ProtobufTrait;
use yii\base\Controller;

class TestController extends Controller
{
    use ProtobufTrait;  //利用trait特性asProtobuf方法注入

    public function init()
    {
        parent::init();
        // 消息文件注册
    }

    public function actionProtobuf(){
        //参数获取
        $params = \Yii::$app->getRequest()->getBodyParams();

        //TODO:逻辑代码

        //结果protobuf序列化，支持属性名下滑线转驼峰式
        $data = [
            'UserInfo'=>[
                'OpenUid'=>'xxxx',
                'NickName'=>'xxxx',
                'Age'=>100,
                'Param1'=>1000
            ],
            'AddrList'=>[
                'home'=>[
                    'Address'=>'addr1',
                    'IphoneNum'=>'153xxxx6476'
                ],
                'company'=>[
                    'Address'=>'addr2',
                    'IphoneNum'=>'188xxxx7049'
                ],
            ],
            'GoneCities'=>[
                ['Name'=>'Beijing'],
                ['Name'=>'Hangzhou'],
            ]
        ];


        return $this->asProtobuf(['class'=>'PbApp\UserLoginACK', 'data'=>$data]);
    }
}
```

结果范例
```text


xxxxxxxxd �
home
153xxxx6476
company
188xxxx7049	
Beijing

Hangzhou
```


### proto范例
扩展完整支持proto3特性
```proto
syntax = "proto3";

package PbApp;

message Request
{
    map<string,string>  Headers  = 1;			// 消息头
    map<string,string>  BodyParams  = 2;         // 具体参数字典
}

message Meta
{
    int32 Param1 = 1;
}

message Reponse
{
    string  ReflectClass    = 1;    //解析是使用的反射类
    bytes   Body            = 2;    //自定义pb数据
    repeated string ArrayParams = 3;
    map<string,Meta> MapParams = 4;
}

message UserInfo
{
    string  OpenUid         = 1;
    string  NickName        = 2;
    int32   Age             = 3;
    int32   Param1          = 4;
}

message Address
{
    string IphoneNum    = 1;
    string  Gender      = 2;
}

message City
{
    string Name     = 1;
}

message UserLoginACK
{
    UserInfo    UserInfo    = 1;
    map<string,Address> AddrList= 2;
    repeated    City   GoneCities = 3;
}

```
