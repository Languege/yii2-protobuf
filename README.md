Yii Protobuf Extension is a wrapper for [php protobuf c-extension](https://github.com/protocolbuffers/protobuf/tree/master/php).It provides an easy way to decode/encoder protobuf data with Yii.In addition to，it provides a tool to generate php proto files from .proto.

You must install php [c-ext](https://github.com/protocolbuffers/protobuf/tree/master/php) before you can use this extension

### Requirements
To use PHP runtime library requires:
- C extension:protobuf >= 3.5.0
- PHP package:php >= 7.0.0
- Yii2.0 or above

### Installation
You can install this extension by composer, as follows:
```bash
composer require language/yii2-protobuf
```

### Configure
You need to add protobuf parser/formatter for request/response Component, as follows:
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
                    'hump'=>true, //By default, the field name is underlined to hump, for example, iphone_num is converted to IphoneNum.
                ],
            ],
        ],
        ...
    ],
]

```
As you can see, this extension use ```application/x-protobuf``` Content-Type to distinguish protobuf binary data.So, Client should set Content-Type as 
```application/x-protobuf``` when it send protobuf binary data to Server

### Generate Proto
You can run build.sh shell script to generate proto files after Editing msg.proto. it will generate ```PbApp``` and ```GPBMetadata```.You should always edit .proto instead of editing generated proto files
 ```shell
bash build.sh
```


### Register Proto
You need to register .proto.php files for encode protobuf data after generate proto files.You can create a base controller and register them, As follows:


```php
class BaseController extends Controller
{
    use ProtobufTrait;  //Inject using the trait attribute asProtobuf method

    public function init()
    {
        parent::init();
        // 消息文件注册
        ProtobufManager::register(ROOT . '/proto/msg.proto.php');
    }
}
```
ProtobufTrait provides ```asProtobuf``` method to convert php hash table to protobuf data


### Usage
You should alway get request params with ```$request->getBodyParams()```intead of ```$_REQUEST```.ProtobufParser parser protobuf to array 
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

    public function actionProtobuf(){
        //params
        $params = \Yii::$app->getRequest()->getBodyParams();

        //TODO:your logic

        //convert array to protobuf
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

Sample
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

### Customized  Request Struct
By default, protobuf parser can only parser map<string,string> protobuf data as message-defined ```proto``` 
```protobuf
message Request
{
    map<string,string>  Headers  = 1;			// Header Params
    map<string,string>  BodyParams  = 2;         // Body Params
}
```

You can define your request proto, as follows
```protobuf
message Meta{
    repeated    Params = 1;
}

message MyRequest
{
     map<string,Meta>  Headers  = 1;			// Header Params
        map<string,Meta>  BodyParams  = 2;         // Body Params
}
```
Then, you should tell ProtobufFormatter which class to serialize Array Data
 ```php
return [
    ...
    'components' => [
            ...
        'response' => [
            'formatters'=>[
                'protobuf' => [
                    'class'=>'Language\Protobuf\ProtobufResponseFormatter',
                    'hump'=>true, //By default, the field name is underlined to hump, for example, iphone_num is converted to IphoneNum.
                    'requestProtoClass'=>'PbApp\MyRequest'
                ],
            ],
        ],
        ...
    ],
]
```

If you need more flexiable data-struct, you can parser the protobuf raw data, as follows:
```php
message UserMsgLoginREQ{
    string  UserName = 1;
    string  Password = 2;
}

message FlexiableRequest
{
    string ProtoClass  = 1;         // proto class to parser
    bytes  ProtoData  = 2;         // bytes protobuf data
}
```

FlexiableRequest is a internal proto define. So, don't change the message name.