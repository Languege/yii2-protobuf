<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/12/1
 * Time: 下午1:19
 */

use Language\Protobuf\ProtobufResponseFormatter;
use Language\Protobuf\ProtobufManager;

class ProtobufResponseFormatterTest extends \PHPUnit\Framework\TestCase
{

    public function testSerialize(){
        require_once dirname(__DIR__) . '/autoload.php';
        require_once dirname(__DIR__ ). '/vendor/autoload.php';
        require_once dirname(dirname(dirname(__DIR__ ))). '/autoload.php';
        require_once dirname(dirname(dirname(__DIR__ ))). '/yiisoft/yii2/Yii.php';

        //注册消息
        ProtobufManager::register(dirname(__DIR__) . '/proto/msg.proto.php');
        $encoder = new ProtobufResponseFormatter();

        $data = [
            'UserInfo'=>[
                'OpenUid'=>'xxxx',
                'NickName'=>'xxxx',
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

        $obj = $encoder->serialize('PbApp\UserLoginACK', $data);

        $this->assertEquals('PbApp\UserLoginACK', get_class($obj));

        if($obj instanceof PbApp\UserLoginACK){

//            echo $obj->serializeToJsonString();
            $this->assertEquals('xxxx', $obj->getUserInfo()->getOpenUid());
            $this->assertEquals('xxxx', $obj->getUserInfo()->getNickName());

            $homeAddr = $obj->getAddrList()['home'];

            $this->assertEquals('PbApp\Address', get_class($homeAddr));

            $city = $obj->getGoneCities()[0];

            $this->assertEquals('PbApp\City', get_class($city));
            if($city instanceof PbApp\City){
                $this->assertEquals('Beijing', $city->getName());
            }
        }
    }
}
